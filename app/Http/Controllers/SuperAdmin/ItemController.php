<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        // HANYA tampilkan item yang tidak di-soft delete
        $totalItems = Item::count();
        $availableItems = Item::available()->count();
        $borrowedItems = Item::borrowed()->count();
        $missingItems = Item::missing()->count();

        return view('superadmin.items.index', [
            'title' => 'Item Management',
            'content' => 'Kelola semua tools dalam sistem',
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'borrowedItems' => $borrowedItems,
            'missingItems' => $missingItems,
        ]);
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        return view('superadmin.items.create', [
            'title' => 'Add New Item'
        ]);
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'epc' => [
                'required',
                'string',
                'max:255',
                'unique:items,epc'
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255'
            ]
        ], [
            'epc.required' => 'EPC field is required.',
            'epc.unique' => 'This EPC already exists in the system.',
            'nama_barang.required' => 'Item name is required.'
        ]);

        try {
            DB::beginTransaction();

            $item = Item::create($validated);
            $currentUser = Auth::user();

            // Create notification
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolAdded($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to create notification for item creation: ' . $notifError->getMessage());
            }

            DB::commit();

            // IMPROVED: Force immediate cache clear
            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            $successMessage = "Item '{$item->nama_barang}' has been added successfully!";
            $stats = $this->getCurrentStats();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'item' => [
                        'id' => $item->id,
                        'epc' => $item->epc,
                        'nama_barang' => $item->nama_barang,
                        'status' => $item->status
                    ],
                    'stats' => $stats,
                    'trigger_refresh' => true,
                    'force_update' => true
                ], 200);
            }

            return redirect()->route('superadmin.items.index')->with('success', $successMessage);
        } catch (ValidationException $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to add item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            $errorMessage = 'Failed to add item. Please try again.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        try {
            // Load semua relasi yang dibutuhkan untuk detail view
            $item->load([
                'borrower',
                'borrower.detail',
                'borrowerDetail',
                'missingTools',
                'activeMissingTool'
            ]);

            // Sync koin untuk user yang meminjam item (jika ada)
            if ($item->user_id && $item->borrower) {
                $borrowerDetail = $item->borrower->detail;
                if ($borrowerDetail) {
                    $adminRoles = [
                        'admin',
                        'superadmin',
                        'super_admin',
                        'Admin',
                        'SuperAdmin',
                        'Super_Admin',
                        'ADMIN',
                        'SUPERADMIN',
                        'SUPER_ADMIN'
                    ];

                    $isAdminUser = in_array(trim($item->borrower->role ?? ''), $adminRoles, true);

                    if (!$isAdminUser) {
                        $oldKoin = $borrowerDetail->koin;
                        $borrowerDetail->syncKoin();

                        Log::info('Synced coin for borrower in item detail view', [
                            'item_id' => $item->id,
                            'borrower_id' => $item->user_id,
                            'old_koin' => $oldKoin,
                            'new_koin' => $borrowerDetail->fresh()->koin,
                            'requested_by_admin' => Auth::id()
                        ]);
                    }
                }
            }

            // Generate HTML untuk modal detail
            $html = view('superadmin.items.detail-partial', compact('item'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'item_data' => [
                    'id' => $item->id,
                    'name' => $item->nama_barang,
                    'status' => $item->status,
                    'borrower_id' => $item->user_id,
                    'has_borrower' => $item->user_id ? true : false,
                    'is_missing' => $item->status === 'missing',
                    'borrower_koin' => $item->borrower && $item->borrower->detail ? $item->borrower->detail->koin : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading item details in superadmin: ' . $e->getMessage(), [
                'item_id' => $item->id,
                'admin_user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load item details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        return view('superadmin.items.edit', [
            'title' => 'Edit Item',
            'item' => $item
        ]);
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'epc' => [
                'required',
                'string',
                'max:255',
                'unique:items,epc,' . $item->id
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255'
            ],
            'status' => [
                'required',
                'in:available,borrowed,missing,out_of_stock'
            ]
        ], [
            'epc.required' => 'EPC field is required.',
            'epc.unique' => 'This EPC already exists in the system.',
            'nama_barang.required' => 'Item name is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.'
        ]);

        try {
            DB::beginTransaction();

            $oldName = $item->nama_barang;
            $oldStatus = $item->status;
            $currentUser = Auth::user();

            // Create notification
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolEdited($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to create notification for item update: ' . $notifError->getMessage());
            }

            // Special handling for status changes
            if ($validated['status'] !== $oldStatus) {
                if ($oldStatus === 'borrowed' && in_array($validated['status'], ['available', 'out_of_stock'])) {
                    $validated['user_id'] = null;
                } elseif ($validated['status'] === 'borrowed' && !$item->user_id) {
                    throw new \Exception('Cannot set status to borrowed without assigning a borrower.');
                } elseif ($oldStatus === 'missing' && $validated['status'] === 'available') {
                    $validated['user_id'] = null;
                }
            }

            $item->update($validated);
            DB::commit();

            // IMPROVED: Force immediate update
            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            $successMessage = "Item '{$oldName}' has been updated successfully!";
            $stats = $this->getCurrentStats();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'item' => $item->fresh(),
                    'stats' => $stats,
                    'trigger_refresh' => true,
                    'force_update' => true
                ]);
            }

            return redirect()->route('superadmin.items.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update item: ' . $e->getMessage());
            $errorMessage = $e->getMessage() ?: 'Failed to update item. Please try again.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * SOFT DELETE: Remove the specified item from storage (soft delete)
     */
    public function destroy(Item $item)
    {
        try {
            DB::beginTransaction();

            $itemName = $item->nama_barang;
            $itemId = $item->id;
            $currentUser = Auth::user();

            // Create notification before deleting
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolDeleted($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to create notification for item deletion: ' . $notifError->getMessage());
            }

            // SOFT DELETE - menggunakan method delete() dari model yang sudah di-override
            $item->delete(); // Ini akan throw exception jika item sedang dipinjam/missing

            DB::commit();

            // IMPROVED: Force immediate cache clear dan timestamp update
            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            $successMessage = "Item '{$itemName}' has been moved to trash successfully!";
            $stats = $this->getCurrentStats();

            Log::info('Item soft deleted successfully', [
                'item_id' => $itemId,
                'item_name' => $itemName,
                'deleted_by' => $currentUser->id ?? 'unknown',
                'new_stats' => $stats
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'stats' => $stats,
                    'trigger_refresh' => true,
                    'force_update' => true,
                    'deleted_item_id' => $itemId
                ], 200);
            }

            return redirect()->route('superadmin.items.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to soft delete item: ' . $e->getMessage(), [
                'item_id' => $item->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Pesan error yang lebih spesifik
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'borrowed') !== false || strpos($errorMessage, 'missing') !== false) {
                // Gunakan pesan error dari model
                $errorMessage = $e->getMessage();
            } else {
                $errorMessage = 'Failed to delete item. Please try again.';
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * NEW: Restore soft deleted item
     */
    public function restore($id)
    {
        try {
            $item = Item::onlyTrashed()->findOrFail($id);

            DB::beginTransaction();

            $item->restore();
            $currentUser = Auth::user();

            // Create notification for restore
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    // Anda bisa membuat method baru di Notification untuk restore
                    // Notification::toolRestored($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to create notification for item restore: ' . $notifError->getMessage());
            }

            DB::commit();

            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            $successMessage = "Item '{$item->nama_barang}' has been restored successfully!";
            $stats = $this->getCurrentStats();

            Log::info('Item restored successfully', [
                'item_id' => $item->id,
                'item_name' => $item->nama_barang,
                'restored_by' => $currentUser->id ?? 'unknown'
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'stats' => $stats,
                    'trigger_refresh' => true,
                    'force_update' => true
                ]);
            }

            return redirect()->route('superadmin.items.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to restore item: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore item. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to restore item. Please try again.');
        }
    }

    /**
     * NEW: Force delete (permanent delete)
     */
    public function forceDestroy($id)
    {
        try {
            $item = Item::onlyTrashed()->findOrFail($id);

            DB::beginTransaction();

            $itemName = $item->nama_barang;
            $itemId = $item->id;
            $currentUser = Auth::user();

            // Force delete permanently
            $item->forceDelete();

            DB::commit();

            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            $successMessage = "Item '{$itemName}' has been permanently deleted!";

            Log::warning('Item permanently deleted', [
                'item_id' => $itemId,
                'item_name' => $itemName,
                'deleted_by' => $currentUser->id ?? 'unknown'
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'trigger_refresh' => true,
                    'force_update' => true
                ]);
            }

            return redirect()->route('superadmin.items.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to force delete item: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to permanently delete item. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to permanently delete item. Please try again.');
        }
    }

    /**
     * NEW: Get deleted items data for DataTables AJAX
     */
    public function getDeletedData(Request $request)
    {
        try {
            $query = Item::onlyTrashed()->select([
                'id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'created_at',
                'updated_at',
                'deleted_at'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('deleted_at_formatted', function ($item) {
                    return $item->deleted_at->format('d M Y, H:i');
                })
                ->addColumn('actions', function ($item) {
                    return '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-success restore-item" 
                                    data-item-id="' . $item->id . '" 
                                    data-item-name="' . e($item->nama_barang) . '"
                                    title="Restore Item">
                                <i class="ti ti-arrow-back-up"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger force-delete-item" 
                                    data-item-id="' . $item->id . '" 
                                    data-item-name="' . e($item->nama_barang) . '"
                                    title="Permanently Delete">
                                <i class="ti ti-trash-x"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Deleted Items Error: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading deleted items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OPTIMIZED: Get items data for DataTables AJAX with better performance
     */
    public function getData(Request $request)
    {
        try {
            // HANYA tampilkan item yang tidak di-soft delete
            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'created_at',
                'updated_at'
            ]);

            // IMPROVED: Add better caching for status-only requests
            if ($request->get('status_only')) {
                $query->select(['id', 'status', 'updated_at']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_text', function ($item) {
                    return $item->status_text;
                })
                ->addColumn('status_badge_class', function ($item) {
                    return $item->status_badge_class;
                })
                ->addColumn('created_at_formatted', function ($item) {
                    return $item->created_at->format('d M Y, H:i');
                })
                ->addColumn('actions', function ($item) {
                    $editUrl = route('superadmin.items.edit', $item->id);

                    $actions = '
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="dropdown">
                                <button class="btn btn-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">
                                    <li>
                                        <a class="dropdown-item" href="' . $editUrl . '">
                                            <i class="ti ti-edit me-2"></i>Edit
                                        </a>
                                    </li>';

                    if ($item->status === 'borrowed') {
                        $actions .= '
                            <li>
                                <a class="dropdown-item text-warning mark-missing" href="#" 
                                   data-item-id="' . $item->id . '" 
                                   data-item-name="' . e($item->nama_barang) . '">
                                    <i class="ti ti-alert-triangle me-2"></i>Mark as Missing
                                </a>
                            </li>';
                    }

                    $actions .= '
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger delete-item" href="#" 
                                   data-item-id="' . $item->id . '" 
                                   data-item-name="' . e($item->nama_barang) . '"
                                   data-item-status="' . $item->status . '">
                                    <i class="ti ti-trash me-2"></i>Move to Trash
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';

                    return $actions;
                })
                ->filter(function ($query) use ($request) {
                    // Status filter
                    if ($request->has('columns') && isset($request->columns[3]['search']['value'])) {
                        $statusFilter = $request->columns[3]['search']['value'];

                        if ($statusFilter && $statusFilter !== '') {
                            $query->where('status', $statusFilter);
                        }
                    }
                })
                ->with([
                    'stats' => $this->getCurrentStats(),
                    'last_db_update' => $this->getLastDatabaseUpdate(),
                    'refresh_timestamp' => now()->toISOString() // IMPROVED: Add explicit refresh timestamp
                ])
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());

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
     * OPTIMIZED: Check for updates dengan database timestamp detection
     */
    public function checkUpdates(Request $request)
    {
        try {
            $clientLastUpdate = $request->get('last_update');
            $hasUpdates = false;
            $updateInfo = [];

            $latestDbUpdate = $this->getLastDatabaseUpdate();
            $currentTime = now()->toISOString();

            // IMPROVED: Better comparison logic
            if ($latestDbUpdate && $clientLastUpdate) {
                try {
                    $dbTime = Carbon::parse($latestDbUpdate);
                    $clientTime = Carbon::parse($clientLastUpdate);

                    // Add a small buffer to avoid false positives (1 second)
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
                    Log::warning('Failed to parse timestamps: ' . $e->getMessage());
                    $hasUpdates = false;
                }
            } elseif ($latestDbUpdate && !$clientLastUpdate) {
                $hasUpdates = true;
            } else {
                $hasUpdates = false;
            }

            // Get stats hanya jika ada update
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
            Log::error('Check updates error: ' . $e->getMessage());

            return response()->json([
                'has_updates' => false,
                'current_time' => now()->toISOString(),
                'error' => 'Failed to check updates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if EPC already exists (for real-time validation)
     */
    public function checkEpc(Request $request)
    {
        $epc = $request->input('epc');
        $itemId = $request->input('item_id');

        if (!$epc) {
            return response()->json([
                'exists' => false,
                'message' => 'EPC is required'
            ]);
        }

        $query = Item::where('epc', $epc);

        if ($itemId) {
            $query->where('id', '!=', $itemId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'EPC already exists' : 'EPC is available'
        ]);
    }

    /**
     * OPTIMIZED: Get last database update timestamp
     */
    private function getLastDatabaseUpdate()
    {
        try {
            // HANYA consider item yang tidak di-soft delete
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
            Log::error('Failed to get database timestamp: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * IMPROVED: Force refresh all connected clients
     */
    public function forceRefresh(Request $request)
    {
        try {
            $this->clearStatsCache();
            $this->updateGlobalTimestamp();

            return response()->json([
                'success' => true,
                'message' => 'Refresh signal sent to all clients',
                'timestamp' => $this->getLastDatabaseUpdate(),
                'stats' => $this->getCurrentStats()
            ]);
        } catch (\Exception $e) {
            Log::error('Force refresh error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send refresh signal'
            ], 500);
        }
    }

    /**
     * OPTIMIZED: Get current system stats with caching
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
            Log::error('Get stats error: ' . $e->getMessage());

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
        return Cache::remember('items_stats', 30, function () {
            return [
                'total_items' => Item::count(),
                'available_items' => Item::available()->count(),
                'borrowed_items' => Item::borrowed()->count(),
                'missing_items' => Item::missing()->count(),
                'out_of_stock_items' => Item::outOfStock()->count(),
                'last_db_update' => $this->getLastDatabaseUpdate(),
                'timestamp' => now()->toISOString()
            ];
        });
    }

    /**
     * HELPER: Clear stats cache
     */
    private function clearStatsCache()
    {
        Cache::forget('items_stats');
        Cache::forget('global_items_timestamp');
    }

    /**
     * HELPER: Update global timestamp for immediate refresh
     */
    private function updateGlobalTimestamp()
    {
        Cache::put('global_items_timestamp', now()->toISOString(), 300); // 5 minutes

        // Optional: Touch a random item to trigger database timestamp update
        $randomItem = Item::inRandomOrder()->first();
        if ($randomItem) {
            $randomItem->touch();
        }
    }
}
