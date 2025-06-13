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

            // Set status as available by default (handled by model default attributes)
            $item = Item::create($validated);

            // Get current authenticated user
            $currentUser = Auth::user();

            // Create notification for tool added - wrap in try-catch
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolAdded($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                // Log notification error but don't fail the whole process
                Log::warning('Failed to create notification for item creation: ' . $notifError->getMessage());
            }

            DB::commit();

            // Clear any output buffers that might interfere
            if (ob_get_level()) {
                ob_clean();
            }

            $successMessage = "Item '{$item->nama_barang}' has been added successfully!";

            // AJAX Response with explicit status code
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
                    'trigger_refresh' => true // Signal for real-time refresh
                ], 200);
            }

            return redirect()
                ->route('superadmin.items.index')
                ->with('success', $successMessage);
        } catch (ValidationException $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
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

            return redirect()
                ->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Display the specified item.
     */
    /**
     * Show item details for superadmin
     * 
     * @param Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Item $item)
    {
        try {
            // Load semua relasi yang dibutuhkan untuk detail view
            $item->load([
                'borrower',              // User yang meminjam
                'borrower.detail',       // Detail user yang meminjam  
                'borrowerDetail',        // Direct relation ke UserDetail
                'missingTools',          // Semua missing tools records
                'activeMissingTool'      // Missing tool yang sedang pending
            ]);

            // Sync koin untuk user yang meminjam item (jika ada)
            if ($item->user_id && $item->borrower) {
                $borrowerDetail = $item->borrower->detail;
                if ($borrowerDetail) {
                    // Check if borrower is admin (tidak perlu sync koin)
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

                    // Sync koin hanya jika bukan admin
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
            // Get current authenticated user
            $currentUser = Auth::user();

            // Create notification for tool added - wrap in try-catch
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolEdited($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                // Log notification error but don't fail the whole process
                Log::warning('Failed to create notification for item creation: ' . $notifError->getMessage());
            }


            // Special handling for status changes
            if ($validated['status'] !== $oldStatus) {
                // If changing from borrowed to available/out_of_stock, clear user_id
                if ($oldStatus === 'borrowed' && in_array($validated['status'], ['available', 'out_of_stock'])) {
                    $validated['user_id'] = null;
                }
                // If changing to borrowed but no user_id assigned, don't allow
                elseif ($validated['status'] === 'borrowed' && !$item->user_id) {
                    throw new \Exception('Cannot set status to borrowed without assigning a borrower.');
                }
                // If changing from missing to available, clear user_id
                elseif ($oldStatus === 'missing' && $validated['status'] === 'available') {
                    $validated['user_id'] = null;
                }
            }

            $item->update($validated);

            DB::commit();

            $successMessage = "Item '{$oldName}' has been updated successfully!";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'item' => $item->fresh(),
                    'trigger_refresh' => true // Signal for real-time refresh
                ]);
            }

            return redirect()
                ->route('superadmin.items.index')
                ->with('success', $successMessage);
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

            return redirect()
                ->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        try {
            // Check if item is currently borrowed or missing
            if ($item->status === 'borrowed' || $item->status === 'missing' || $item->user_id) {
                $message = match ($item->status) {
                    'borrowed' => 'Cannot delete item that is currently borrowed. Please return the item first.',
                    'missing' => 'Cannot delete item that is missing. Please reclaim the item first or wait for resolution.',
                    default => 'Cannot delete item that is currently in use.'
                };

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }

                return redirect()
                    ->back()
                    ->with('error', $message);
            }

            DB::beginTransaction();

            $itemName = $item->nama_barang;
            $currentUser = Auth::user();

            // Create notification before deleting - wrap in try-catch
            try {
                if ($currentUser && class_exists('App\Models\Notification')) {
                    Notification::toolDeleted($item, $currentUser);
                }
            } catch (\Exception $notifError) {
                // Log notification error but don't fail the deletion
                Log::warning('Failed to create notification for item deletion: ' . $notifError->getMessage());
            }

            $item->delete();

            DB::commit();

            $successMessage = "Item '{$itemName}' has been deleted successfully!";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'trigger_refresh' => true // Signal for real-time refresh
                ]);
            }

            return redirect()
                ->route('superadmin.items.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete item: ' . $e->getMessage());
            $errorMessage = 'Failed to delete item. Please try again.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Get items data for DataTables AJAX
     */
    public function getData(Request $request)
    {
        try {
            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'created_at',
                'updated_at'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_text', function ($item) {
                    // Get human-readable status text
                    return $item->status_text;
                })
                ->addColumn('status_badge_class', function ($item) {
                    // Get bootstrap badge class for status
                    return $item->status_badge_class;
                })
                ->addColumn('created_at_formatted', function ($item) {
                    return $item->created_at->format('d M Y, H:i');
                })
                ->addColumn('actions', function ($item) {
                    $editUrl = route('superadmin.items.edit', $item->id);
                    $deleteUrl = route('superadmin.items.destroy', $item->id);

                    $actions = '
                        <div class="btn-group" role="group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-primary">
                                <i class="ti ti-edit"></i>
                            </a>';

                    // Add mark as missing button if item is borrowed
                    if ($item->status === 'borrowed') {
                        $actions .= '
                            <button type="button" class="btn btn-sm btn-warning mark-missing" 
                                    data-item-id="' . $item->id . '" 
                                    data-item-name="' . e($item->nama_barang) . '"
                                    title="Mark as Missing">
                                <i class="ti ti-alert-triangle"></i>
                            </button>';
                    }

                    // Only show delete button if item is not borrowed or missing
                    if ($item->status !== 'borrowed' && $item->status !== 'missing') {
                        $actions .= '
                            <form method="POST" action="' . $deleteUrl . '" style="display: inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="button" class="btn btn-sm btn-danger delete-item" 
                                        data-item-name="' . e($item->nama_barang) . '">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>';
                    } else {
                        // Show disabled delete button for borrowed/missing items
                        $disabledReason = $item->status === 'borrowed' ? 'Cannot delete borrowed item' : 'Cannot delete missing item';
                        $actions .= '
                            <button type="button" class="btn btn-sm btn-secondary" disabled 
                                    title="' . $disabledReason . '">
                                <i class="ti ti-trash"></i>
                            </button>';
                    }

                    $actions .= '
                        </div>
                    ';

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
                    'stats' => [
                        'total_items' => Item::count(),
                        'available_items' => Item::available()->count(),
                        'borrowed_items' => Item::borrowed()->count(),
                        'missing_items' => Item::missing()->count(),
                    ],
                    'last_db_update' => $this->getLastDatabaseUpdate()
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
     * Change item status (for borrowing/returning/marking missing)
     */
    public function changeStatus(Request $request, Item $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,borrowed,missing,out_of_stock',
            'user_id' => 'nullable|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $item->status;

            // Handle status transitions
            $updateData = ['status' => $validated['status']];

            // Set user_id based on status
            if ($validated['status'] === 'borrowed') {
                if (!isset($validated['user_id'])) {
                    throw new \Exception('User ID is required when setting status to borrowed.');
                }
                $updateData['user_id'] = $validated['user_id'];
            } else {
                // For available, missing, or out_of_stock, clear user_id
                $updateData['user_id'] = null;
            }

            // Update item
            $item->update($updateData);

            DB::commit();

            $statusText = match ($validated['status']) {
                'available' => 'available',
                'borrowed' => 'borrowed',
                'missing' => 'missing',
                'out_of_stock' => 'out of stock'
            };

            $message = "Item '{$item->nama_barang}' status changed from {$oldStatus} to {$statusText}.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => $item->fresh(),
                'trigger_refresh' => true // Signal for real-time refresh
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to change item status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to change item status.'
            ], 500);
        }
    }

    /**
     * IMPROVED: Check for updates dengan database timestamp detection
     */
    public function checkUpdates(Request $request)
    {
        try {
            $clientLastUpdate = $request->get('last_update');
            $hasUpdates = false;
            $updateInfo = [];

            // NEW: Get latest database update timestamp
            $latestDbUpdate = $this->getLastDatabaseUpdate();
            $currentTime = now()->toISOString();

            // Log untuk debugging
            Log::info('CheckUpdates called', [
                'client_last_update' => $clientLastUpdate,
                'latest_db_update' => $latestDbUpdate,
                'current_time' => $currentTime
            ]);

            // Compare client timestamp dengan database timestamp
            if ($latestDbUpdate && $clientLastUpdate) {
                try {
                    $dbTime = Carbon::parse($latestDbUpdate);
                    $clientTime = Carbon::parse($clientLastUpdate);

                    // Ada update jika database timestamp lebih baru dari client
                    $hasUpdates = $dbTime->greaterThan($clientTime);

                    Log::info('Database vs Client time comparison', [
                        'db_time' => $dbTime->toISOString(),
                        'client_time' => $clientTime->toISOString(),
                        'has_updates' => $hasUpdates
                    ]);

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
                // Jika client belum ada timestamp, ada update
                $hasUpdates = true;
                Log::info('Client has no timestamp, forcing update');
            } else {
                // Tidak ada update
                $hasUpdates = false;
                Log::info('No database update or both empty, no updates');
            }

            // Get stats jika ada update
            $currentStats = null;
            if ($hasUpdates) {
                $currentStats = [
                    'total_items' => Item::count(),
                    'available_items' => Item::available()->count(),
                    'borrowed_items' => Item::borrowed()->count(),
                    'missing_items' => Item::missing()->count(),
                ];

                Log::info('Database changes detected, sending new stats', $currentStats);
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
     * NEW: Get last database update timestamp dari actual database
     * Ini akan mendeteksi perubahan langsung di database (bukan hanya dari controller)
     */
    private function getLastDatabaseUpdate()
    {
        try {
            // Get latest updated_at dari tabel items
            $latestUpdate = Item::max('updated_at');

            // Get latest created_at juga untuk item baru
            $latestCreated = Item::max('created_at');

            // Ambil yang paling baru
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

            $timestamp = $latest ? Carbon::parse($latest)->toISOString() : null;

            Log::info('Database timestamp check', [
                'latest_updated_at' => $latestUpdate,
                'latest_created_at' => $latestCreated,
                'final_timestamp' => $timestamp
            ]);

            return $timestamp;
        } catch (\Exception $e) {
            Log::error('Failed to get database timestamp: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Force refresh all connected clients
     */
    public function forceRefresh(Request $request)
    {
        try {
            // Force update dengan mengubah updated_at salah satu item
            $latestItem = Item::latest('updated_at')->first();
            if ($latestItem) {
                $latestItem->touch(); // Update updated_at timestamp
            }

            return response()->json([
                'success' => true,
                'message' => 'Refresh signal sent to all clients',
                'timestamp' => $this->getLastDatabaseUpdate()
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
     * Get current system stats
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_items' => Item::count(),
                'available_items' => Item::available()->count(),
                'borrowed_items' => Item::borrowed()->count(),
                'missing_items' => Item::missing()->count(),
                'out_of_stock_items' => Item::outOfStock()->count(),
                'last_db_update' => $this->getLastDatabaseUpdate(),
                'timestamp' => now()->toISOString()
            ];

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
}
