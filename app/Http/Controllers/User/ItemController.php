<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ItemController extends Controller
{
    // Cache configuration constants
    const CACHE_TTL_STATS = 60;           // 1 minute for stats
    const CACHE_TTL_UPDATES = 30;         // 30 seconds for update checks
    const CACHE_TTL_DATA = 45;            // 45 seconds for table data
    const CACHE_TAG_ITEMS = 'user_items';
    const CACHE_TAG_STATS = 'user_stats';

    /**
     * Display a listing of the items (view-only for users).
     */
    public function index()
    {
        $stats = $this->getCurrentStats();

        return view('user.items.index', [
            'title' => 'Tool Stocks',
            'content' => 'View available tools and their current status',
            'totalItems' => $stats['total_items'],
            'availableItems' => $stats['available_items'],
            'borrowedItems' => $stats['borrowed_items'],
            'missingItems' => $stats['missing_items'],
        ]);
    }

    /**
     * Get items data for DataTables AJAX with advanced caching
     */
    public function getData(Request $request)
    {
        try {
            // Generate cache key based on request parameters
            $cacheKey = $this->generateDataCacheKey($request);

            // For real-time updates, check if we should bypass cache
            $bypassCache = $request->get('bypass_cache', false) || $request->get('_', false);

            if (!$bypassCache) {
                $cachedData = Cache::tags([self::CACHE_TAG_ITEMS])->get($cacheKey);
                if ($cachedData) {
                    // Add fresh metadata to cached data
                    $cachedData['stats'] = $this->getCurrentStats();
                    $cachedData['last_db_update'] = $this->getLastDatabaseUpdate();
                    $cachedData['refresh_timestamp'] = now()->toISOString();
                    $cachedData['cached'] = true;

                    return response()->json($cachedData);
                }
            }

            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'created_at',
                'updated_at'
            ])->with(['borrower:id,name']);

            // Optimized query for status-only requests
            if ($request->get('status_only')) {
                $query->select(['id', 'status', 'updated_at', 'user_id']);
            }

            $dataTable = DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('borrower_name', function ($item) {
                    return $item->borrower ? $item->borrower->name : '';
                })
                ->addColumn('updated_at_formatted', function ($item) {
                    return $item->updated_at->format('d M Y, H:i');
                })
                ->addColumn('status_text', function ($item) {
                    return $this->getStatusText($item->status);
                })
                ->addColumn('status_badge_class', function ($item) {
                    return $this->getStatusBadgeClass($item->status);
                })
                ->filter(function ($query) use ($request) {
                    $this->applyFilters($query, $request);
                })
                ->with([
                    'stats' => $this->getCurrentStats(),
                    'last_db_update' => $this->getLastDatabaseUpdate(),
                    'refresh_timestamp' => now()->toISOString(),
                    'cached' => false
                ]);

            $result = $dataTable->make(true);

            // Cache the result for future requests (only if not bypassing cache)
            if (!$bypassCache) {
                Cache::tags([self::CACHE_TAG_ITEMS])->put($cacheKey, $result->getData(true), self::CACHE_TTL_DATA);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('User DataTables Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * REAL-TIME: Check for updates with intelligent caching
     */
    public function checkUpdates(Request $request)
    {
        try {
            $clientLastUpdate = $request->get('last_update');
            $cacheKey = "user_updates_check:" . md5($clientLastUpdate ?: 'initial');

            // Check cache first for update status
            $cachedUpdate = Cache::tags([self::CACHE_TAG_STATS])->get($cacheKey);
            if ($cachedUpdate && !$request->get('force_check')) {
                return response()->json($cachedUpdate);
            }

            $hasUpdates = false;
            $updateInfo = [];
            $latestDbUpdate = $this->getLastDatabaseUpdate();
            $currentTime = now()->toISOString();

            // Intelligent update detection
            if ($latestDbUpdate && $clientLastUpdate) {
                try {
                    $dbTime = Carbon::parse($latestDbUpdate);
                    $clientTime = Carbon::parse($clientLastUpdate);

                    // Smart comparison with buffer
                    $hasUpdates = $dbTime->greaterThan($clientTime->addSecond());

                    if ($hasUpdates) {
                        // Get update context for user notification
                        $updateInfo = $this->getUpdateContext($clientTime);

                        // Clear relevant caches when updates detected
                        $this->clearUserCaches(['data']);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse timestamps in user view: ' . $e->getMessage());
                    $hasUpdates = false;
                }
            } elseif ($latestDbUpdate && !$clientLastUpdate) {
                $hasUpdates = true;
            }

            // Get fresh stats only if there are updates
            $currentStats = null;
            if ($hasUpdates) {
                $currentStats = $this->getCurrentStats(true); // Force fresh
            }

            $response = [
                'has_updates' => $hasUpdates,
                'current_time' => $currentTime,
                'latest_db_update' => $latestDbUpdate,
                'client_last_update' => $clientLastUpdate,
                'updates' => $updateInfo,
                'stats' => $currentStats,
                'cache_info' => [
                    'detection_method' => 'database_timestamp_user_view',
                    'cached_at' => now()->toISOString(),
                    'ttl' => self::CACHE_TTL_UPDATES
                ]
            ];

            // Cache the response
            Cache::tags([self::CACHE_TAG_STATS])->put($cacheKey, $response, self::CACHE_TTL_UPDATES);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('User check updates error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'client_last_update' => $clientLastUpdate ?? 'none'
            ]);

            return response()->json([
                'has_updates' => false,
                'current_time' => now()->toISOString(),
                'error' => 'Failed to check updates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current system stats with smart caching
     */
    public function getStats(Request $request)
    {
        try {
            $forceFresh = $request->get('force_fresh', false);
            $stats = $this->getCurrentStats($forceFresh);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString(),
                'cached' => !$forceFresh
            ]);
        } catch (\Exception $e) {
            Log::error('User get stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }

    /**
     * Force refresh with cache invalidation
     */
    public function forceRefresh(Request $request)
    {
        try {
            // Clear all user caches
            $this->clearUserCaches();

            // Force fresh data
            $stats = $this->getCurrentStats(true);
            $timestamp = $this->getLastDatabaseUpdate();

            return response()->json([
                'success' => true,
                'message' => 'Data refreshed successfully',
                'timestamp' => $timestamp,
                'stats' => $stats,
                'cache_cleared' => true
            ]);
        } catch (\Exception $e) {
            Log::error('User force refresh error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data'
            ], 500);
        }
    }

    /**
     * PERFORMANCE: Get current stats with intelligent caching
     */
    private function getCurrentStats($forceFresh = false)
    {
        $cacheKey = 'user_items_stats';

        if ($forceFresh) {
            Cache::tags([self::CACHE_TAG_STATS])->forget($cacheKey);
        }

        return Cache::tags([self::CACHE_TAG_STATS])->remember($cacheKey, self::CACHE_TTL_STATS, function () {
            $stats = [
                'total_items' => Item::count(),
                'available_items' => Item::available()->count(),
                'borrowed_items' => Item::borrowed()->count(),
                'missing_items' => Item::missing()->count(),
                'out_of_stock_items' => Item::where('status', 'out_of_stock')->count(),
                'last_db_update' => $this->getLastDatabaseUpdate(),
                'generated_at' => now()->toISOString()
            ];

            // Add percentage calculations for better UX
            $total = $stats['total_items'];
            if ($total > 0) {
                $stats['available_percentage'] = round(($stats['available_items'] / $total) * 100, 1);
                $stats['borrowed_percentage'] = round(($stats['borrowed_items'] / $total) * 100, 1);
                $stats['missing_percentage'] = round(($stats['missing_items'] / $total) * 100, 1);
            } else {
                $stats['available_percentage'] = 0;
                $stats['borrowed_percentage'] = 0;
                $stats['missing_percentage'] = 0;
            }

            return $stats;
        });
    }

    /**
     * PERFORMANCE: Get last database update with caching
     */
    private function getLastDatabaseUpdate()
    {
        $cacheKey = 'user_last_db_update';

        return Cache::tags([self::CACHE_TAG_STATS])->remember($cacheKey, 30, function () {
            try {
                $latestUpdate = Item::max('updated_at');
                $latestCreated = Item::max('created_at');

                $latest = null;
                if ($latestUpdate && $latestCreated) {
                    $latest = Carbon::parse($latestUpdate)->greaterThan(Carbon::parse($latestCreated))
                        ? $latestUpdate
                        : $latestCreated;
                } elseif ($latestUpdate) {
                    $latest = $latestUpdate;
                } elseif ($latestCreated) {
                    $latest = $latestCreated;
                }

                return $latest ? Carbon::parse($latest)->toISOString() : null;
            } catch (\Exception $e) {
                Log::error('Failed to get database timestamp in user view: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * HELPER: Generate cache key for DataTables data
     */
    private function generateDataCacheKey(Request $request)
    {
        $params = [
            'draw' => $request->get('draw'),
            'start' => $request->get('start'),
            'length' => $request->get('length'),
            'order' => $request->get('order'),
            'search' => $request->get('search'),
            'columns' => $request->get('columns'),
            'status_only' => $request->get('status_only', false)
        ];

        return 'user_items_data:' . md5(json_encode($params));
    }

    /**
     * HELPER: Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Status filter
        if ($request->has('columns') && isset($request->columns[3]['search']['value'])) {
            $statusFilter = $request->columns[3]['search']['value'];
            if ($statusFilter && $statusFilter !== '') {
                $query->where('status', $statusFilter);
            }
        }

        // Search filter
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('nama_barang', 'like', "%{$searchValue}%")
                    ->orWhere('epc', 'like', "%{$searchValue}%");
            });
        }
    }

    /**
     * HELPER: Get update context for notifications
     */
    private function getUpdateContext($clientTime)
    {
        try {
            $recentChanges = Item::where('updated_at', '>', $clientTime)
                ->orWhere('created_at', '>', $clientTime)
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get(['id', 'nama_barang', 'status', 'updated_at', 'created_at']);

            if ($recentChanges->isEmpty()) {
                return [];
            }

            return $recentChanges->map(function ($item) use ($clientTime) {
                $isNew = $item->created_at > $clientTime;
                return [
                    'id' => $item->id,
                    'name' => $item->nama_barang,
                    'status' => $item->status,
                    'action' => $isNew ? 'added' : 'updated',
                    'time' => $item->updated_at->toISOString(),
                    'friendly_time' => $item->updated_at->diffForHumans()
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::warning('Failed to get update context: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * HELPER: Get status text
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'available' => 'Available',
            'borrowed' => 'Borrowed',
            'missing' => 'Missing',
            'out_of_stock' => 'Out of Stock'
        ];

        return $statusTexts[$status] ?? 'Unknown';
    }

    /**
     * HELPER: Get status badge class
     */
    private function getStatusBadgeClass($status)
    {
        $classes = [
            'available' => 'bg-success',
            'borrowed' => 'bg-warning',
            'missing' => 'bg-dark',
            'out_of_stock' => 'bg-danger'
        ];

        return $classes[$status] ?? 'bg-secondary';
    }

    /**
     * CACHE MANAGEMENT: Clear user caches
     */
    public function clearUserCaches($types = ['all'])
    {
        try {
            if (in_array('all', $types) || in_array('stats', $types)) {
                Cache::tags([self::CACHE_TAG_STATS])->flush();
                Log::info('User stats cache cleared');
            }

            if (in_array('all', $types) || in_array('data', $types)) {
                Cache::tags([self::CACHE_TAG_ITEMS])->flush();
                Log::info('User items data cache cleared');
            }

            // Clear specific keys
            $specificKeys = [
                'user_items_stats',
                'user_last_db_update',
                'user_global_items_timestamp'
            ];

            foreach ($specificKeys as $key) {
                Cache::forget($key);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear user caches: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * STATIC: Clear user stats cache (called from admin actions)
     */
    public static function clearUserStatsCache()
    {
        try {
            Cache::tags([self::CACHE_TAG_STATS, self::CACHE_TAG_ITEMS])->flush();

            $specificKeys = [
                'user_items_stats',
                'user_last_db_update',
                'user_global_items_timestamp'
            ];

            foreach ($specificKeys as $key) {
                Cache::forget($key);
            }

            Log::info('User cache cleared from admin action');
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear user cache from admin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * CACHE WARMING: Pre-populate cache with fresh data
     */
    public function warmCache()
    {
        try {
            // Warm up stats cache
            $this->getCurrentStats(true);

            // Warm up database timestamp cache
            $this->getLastDatabaseUpdate();

            Log::info('User cache warmed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Cache warmed successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to warm user cache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to warm cache'
            ], 500);
        }
    }

    /**
     * DEBUG: Get cache info for debugging
     */
    public function getCacheInfo()
    {
        if (!config('app.debug')) {
            return response()->json(['error' => 'Debug mode required'], 403);
        }

        $cacheInfo = [
            'stats_cached' => Cache::tags([self::CACHE_TAG_STATS])->has('user_items_stats'),
            'last_update_cached' => Cache::tags([self::CACHE_TAG_STATS])->has('user_last_db_update'),
            'cache_tags' => [
                'stats' => self::CACHE_TAG_STATS,
                'items' => self::CACHE_TAG_ITEMS
            ],
            'cache_ttl' => [
                'stats' => self::CACHE_TTL_STATS,
                'data' => self::CACHE_TTL_DATA,
                'updates' => self::CACHE_TTL_UPDATES
            ],
            'timestamp' => now()->toISOString()
        ];

        return response()->json($cacheInfo);
    }
}
