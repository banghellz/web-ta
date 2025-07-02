<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display guest dashboard with tool statistics
     */
    public function index(Request $request)
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get statistics for dashboard
        $totalItems = Item::count();
        $availableItems = Item::available()->count();
        $borrowedItems = Item::borrowed()->count();
        $missingItems = Item::missing()->count();

        return view('guest.dashboard.index', [
            'title' => 'Dashboard - Tool Overview',
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'borrowedItems' => $borrowedItems,
            'missingItems' => $missingItems,
        ]);
    }

    /**
     * Get tools data for DataTables AJAX (guest view - read-only)
     */
    public function getToolsData(Request $request)
    {
        try {
            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'status',
                'created_at',
                'updated_at'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('updated_at_formatted', function ($item) {
                    return $item->updated_at->format('d M Y, H:i');
                })
                ->filter(function ($query) use ($request) {
                    // Status filter
                    if ($request->has('columns') && isset($request->columns[2]['search']['value'])) {
                        $statusFilter = $request->columns[2]['search']['value'];
                        if ($statusFilter && $statusFilter !== '') {
                            $query->where('status', $statusFilter);
                        }
                    }

                    // Global search in tool name and EPC
                    if ($request->has('search') && $request->search['value']) {
                        $searchValue = $request->search['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('nama_barang', 'like', "%{$searchValue}%")
                                ->orWhere('epc', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->with([
                    'stats' => $this->getCurrentStats(),
                    'last_db_update' => $this->getLastDatabaseUpdate(),
                    'refresh_timestamp' => now()->toISOString()
                ])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Guest DataTables Error: ' . $e->getMessage());

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
     * Check for updates (real-time functionality for guest)
     */
    public function checkUpdates(Request $request)
    {
        try {
            $clientLastUpdate = $request->get('last_update');
            $hasUpdates = false;
            $updateInfo = [];

            $latestDbUpdate = $this->getLastDatabaseUpdate();
            $currentTime = now()->toISOString();

            // Compare timestamps to detect updates
            if ($latestDbUpdate && $clientLastUpdate) {
                try {
                    $dbTime = Carbon::parse($latestDbUpdate);
                    $clientTime = Carbon::parse($clientLastUpdate);

                    // Add buffer to avoid false positives
                    $hasUpdates = $dbTime->greaterThan($clientTime->addSecond());

                    if ($hasUpdates) {
                        // Get recent changes for context
                        $recentChanges = Item::where('updated_at', '>', $clientTime)
                            ->orWhere('created_at', '>', $clientTime)
                            ->orderBy('updated_at', 'desc')
                            ->limit(5)
                            ->get(['id', 'nama_barang', 'status', 'updated_at', 'created_at']);

                        if ($recentChanges->isNotEmpty()) {
                            $updateInfo = $recentChanges->map(function ($item) use ($clientTime) {
                                $isNew = $item->created_at > $clientTime;
                                return [
                                    'id' => $item->id,
                                    'name' => $item->nama_barang,
                                    'status' => $item->status,
                                    'action' => $isNew ? 'created' : 'updated',
                                    'time' => $item->updated_at->toISOString()
                                ];
                            })->toArray();
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse timestamps in guest check: ' . $e->getMessage());
                    $hasUpdates = false;
                }
            } elseif ($latestDbUpdate && !$clientLastUpdate) {
                $hasUpdates = true;
            }

            // Get stats only if there are updates
            $currentStats = null;
            if ($hasUpdates) {
                $currentStats = $this->getCurrentStats();
            }

            return response()->json([
                'has_updates' => $hasUpdates,
                'current_time' => $currentTime,
                'latest_db_update' => $latestDbUpdate,
                'client_last_update' => $clientLastUpdate,
                'updates' => $updateInfo,
                'stats' => $currentStats,
                'debug' => [
                    'detection_method' => 'database_timestamp',
                    'latest_item_update' => $latestDbUpdate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Guest check updates error: ' . $e->getMessage());

            return response()->json([
                'has_updates' => false,
                'current_time' => now()->toISOString(),
                'error' => 'Failed to check updates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current system stats
     */
    public function getStats()
    {
        try {
            $stats = $this->getCurrentStats();

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Guest get stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }

    /**
     * HELPER: Get current stats with caching
     */
    private function getCurrentStats()
    {
        return Cache::remember('guest_tools_stats', 60, function () {
            return [
                'total_items' => Item::count(),
                'available_items' => Item::available()->count(),
                'borrowed_items' => Item::borrowed()->count(),
                'missing_items' => Item::missing()->count(),
                'last_db_update' => $this->getLastDatabaseUpdate(),
                'timestamp' => now()->toISOString()
            ];
        });
    }

    /**
     * HELPER: Get last database update timestamp
     */
    private function getLastDatabaseUpdate()
    {
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
            Log::error('Failed to get database timestamp in guest controller: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check for status changes only - for advanced real-time functionality
     */
    public function checkStatusUpdates(Request $request)
    {
        try {
            // Get current item statuses from database
            $currentStatuses = Item::select('id', 'status', 'updated_at')
                ->get()
                ->keyBy('id')
                ->map(function ($item) {
                    return [
                        'status' => $item->status,
                        'updated_at' => $item->updated_at->toISOString()
                    ];
                })
                ->toArray();

            // Get client's current statuses from request
            $clientStatuses = $request->get('current_statuses', []);

            // Find items with status changes
            $changedItems = [];
            $hasChanges = false;

            foreach ($currentStatuses as $itemId => $dbData) {
                $dbStatus = $dbData['status'];
                $clientStatus = $clientStatuses[$itemId] ?? null;

                // If client doesn't have this item or status is different
                if ($clientStatus === null || $clientStatus !== $dbStatus) {
                    $changedItems[] = [
                        'id' => $itemId,
                        'status' => $dbStatus,
                        'updated_at' => $dbData['updated_at']
                    ];
                    $hasChanges = true;
                }
            }

            // Check for deleted items (items that client has but db doesn't)
            foreach ($clientStatuses as $itemId => $clientStatus) {
                if (!isset($currentStatuses[$itemId])) {
                    // Item was deleted
                    $hasChanges = true;
                }
            }

            // Get current stats only if there are changes
            $currentStats = null;
            if ($hasChanges) {
                $currentStats = $this->getCurrentStats();
            }

            // Prepare response
            $response = [
                'has_status_changes' => $hasChanges,
                'changed_items' => $changedItems,
                'current_statuses' => array_map(function ($data) {
                    return $data['status'];
                }, $currentStatuses),
                'stats' => $currentStats,
                'timestamp' => now()->toISOString()
            ];

            // Add debug info if needed
            if (config('app.debug')) {
                $response['debug'] = [
                    'total_db_items' => count($currentStatuses),
                    'total_client_items' => count($clientStatuses),
                    'changed_count' => count($changedItems)
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Guest check status updates error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'has_status_changes' => false,
                'error' => 'Failed to check status updates'
            ], 500);
        }
    }
}
