<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MissingTools;
use App\Models\Notification;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MissingToolsController extends Controller
{
    /**
     * Display missing tools index page
     */
    public function index()
    {
        return view('admin.missing-tools.index', [
            'title' => 'Missing Tools Management',
            'content' => 'Manage and track missing tools in the system'
        ]);
    }

    /**
     * Get missing tools data for DataTables AJAX
     */
    public function getData(Request $request)
    {
        $query = MissingTools::with(['user', 'user.detail', 'userDetail'])
            ->select([
                'missing_tools.*'
            ]);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_detail', function ($missingTool) {
                // Try multiple ways to get user detail
                if ($missingTool->userDetail) {
                    return $missingTool->userDetail;
                } elseif ($missingTool->user && $missingTool->user->detail) {
                    return $missingTool->user->detail;
                } elseif ($missingTool->user_id) {
                    // Fallback: manual query
                    return \App\Models\UserDetail::where('user_id', $missingTool->user_id)->first();
                }
                return null;
            })
            ->addColumn('user_info', function ($missingTool) {
                $userDetail = null;

                // Try multiple ways to get user detail
                if ($missingTool->userDetail) {
                    $userDetail = $missingTool->userDetail;
                } elseif ($missingTool->user && $missingTool->user->detail) {
                    $userDetail = $missingTool->user->detail;
                } elseif ($missingTool->user_id) {
                    $userDetail = \App\Models\UserDetail::where('user_id', $missingTool->user_id)->first();
                }

                if ($userDetail) {
                    return [
                        'nama' => $userDetail->nama,
                        'nim' => $userDetail->nim,
                        'prodi' => $userDetail->prodi
                    ];
                }

                return ['nama' => 'Unknown User', 'nim' => 'N/A', 'prodi' => 'N/A'];
            })
            ->addColumn('reported_at_formatted', function ($missingTool) {
                return $missingTool->reported_at ? $missingTool->reported_at->format('d M Y, H:i') : 'N/A';
            })
            ->addColumn('reclaimed_at_formatted', function ($missingTool) {
                return $missingTool->reclaimed_at ? $missingTool->reclaimed_at->format('d M Y, H:i') : null;
            })
            ->addColumn('duration_text', function ($missingTool) {
                return $missingTool->duration_text;
            })
            ->with([
                'stats' => [
                    'pending_count' => MissingTools::where('status', 'pending')->count(),
                    'completed_count' => MissingTools::where('status', 'completed')->count(),
                    'cancelled_count' => MissingTools::where('status', 'cancelled')->count(),
                    'total_count' => MissingTools::where('status', '=!', 'cancelled')->count(),
                    'avg_duration' => MissingTools::selectRaw('AVG(DATEDIFF(COALESCE(reclaimed_at, NOW()), reported_at)) as avg_duration')
                        ->value('avg_duration') ? round(MissingTools::selectRaw('AVG(DATEDIFF(COALESCE(reclaimed_at, NOW()), reported_at)) as avg_duration')
                            ->value('avg_duration'), 1) : 0
                ]
            ])
            ->make(true);
    }

    /**
     * Mark an item as missing
     * 
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsMissing($itemId)
    {
        try {
            DB::beginTransaction();

            // Ambil data item berdasarkan ID
            $item = Item::find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Validasi: cek jika item sudah berstatus missing
            if ($item->status === 'missing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item is already marked as missing'
                ], 400);
            }

            // Validasi: item harus sedang dipinjam (ada user_id)
            if (!$item->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item is not currently borrowed by anyone'
                ], 400);
            }

            // Salin data ke tabel missing_tools
            $missingTool = MissingTools::create([
                'item_id' => $item->id,
                'epc' => $item->epc,
                'nama_barang' => $item->nama_barang,
                'user_id' => $item->user_id,
                'status' => 'pending',
                'reported_at' => now()
            ]);

            // Update status item menjadi missing
            // Jangan hapus user_id, tetap tersimpan sebagai penanda tanggung jawab
            $item->update(['status' => 'missing']);

            // Commit transaction dahulu sebelum notification
            DB::commit();

            // Handle notification dengan try-catch terpisah
            // Agar error notification tidak mempengaruhi response utama
            try {
                if (class_exists('App\Models\Notification')) {
                    $currentUser = Auth::user();
                    Notification::toolMissing($missingTool, $currentUser);
                }
            } catch (\Exception $notifError) {
                // Log notification error tapi jangan fail main process
                Log::warning('Failed to create notification for missing tool: ' . $notifError->getMessage(), [
                    'missing_tool_id' => $missingTool->id,
                    'item_id' => $item->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->nama_barang}' has been successfully marked as missing",
                'data' => [
                    'missing_tool' => $missingTool,
                    'item' => $item->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to mark item as missing: ' . $e->getMessage(), [
                'item_id' => $itemId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking item as missing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reclaim a missing item (mark as completed and release user responsibility)
     * 
     * @param int $missingToolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reclaimMissingItem($missingToolId)
    {
        try {
            DB::beginTransaction();

            // Ambil data dari missing_tools
            $missingTool = MissingTools::find($missingToolId);

            if (!$missingTool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing tool data not found'
                ], 404);
            }

            // Validasi: hanya bisa reclaim jika status masih pending
            if ($missingTool->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing tool has already been reclaimed'
                ], 400);
            }

            // Ambil item terkait
            $item = Item::find($missingTool->item_id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Related item not found'
                ], 404);
            }

            // Simpan user_id lama untuk sync koin
            $oldUserId = $item->user_id;
            $oldUser = $item->user; // Simpan user object untuk notification

            // Update user_id di tabel items menjadi null (user tidak bertanggung jawab lagi)
            $item->update(['user_id' => null]);

            // Update status missing_tools menjadi completed
            $missingTool->update([
                'status' => 'completed',
                'reclaimed_at' => now()
            ]);

            // Sync koin untuk user yang sebelumnya meminjam
            if ($oldUserId) {
                $userDetail = UserDetail::where('user_id', $oldUserId)->first();
                if ($userDetail) {
                    $userDetail->syncKoin();
                }
            }

            // Commit transaction dahulu
            DB::commit();

            // Handle notification dengan try-catch terpisah
            try {
                if (class_exists('App\Models\Notification') && $oldUser) {
                    $currentUser = Auth::user();
                    Notification::toolReclaimed($missingTool, $currentUser);
                }
            } catch (\Exception $notifError) {
                // Log notification error tapi jangan fail main process
                Log::warning('Failed to create notification for reclaimed tool: ' . $notifError->getMessage(), [
                    'missing_tool_id' => $missingTool->id,
                    'item_id' => $item->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Missing tool '{$missingTool->nama_barang}' has been successfully reclaimed",
                'data' => [
                    'missing_tool' => $missingTool->fresh(),
                    'item' => $item->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to reclaim missing tool: ' . $e->getMessage(), [
                'missing_tool_id' => $missingToolId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while reclaiming missing tool: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a missing tool report (restore item to available/borrowed status)
     * 
     * @param int $missingToolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelMissingTool($missingToolId)
    {
        try {
            DB::beginTransaction();

            // Ambil data dari missing_tools
            $missingTool = MissingTools::find($missingToolId);

            if (!$missingTool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing tool data not found'
                ], 404);
            }

            // Validasi: hanya bisa cancel jika status masih pending
            if ($missingTool->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing tool cannot be cancelled because status is not pending'
                ], 400);
            }

            // Ambil item terkait
            $item = Item::find($missingTool->item_id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Related item not found'
                ], 404);
            }

            // Tentukan status item yang akan dikembalikan
            // Jika ada user_id, berarti item sedang dipinjam (borrowed)
            // Jika tidak ada user_id, kembalikan ke available
            $newItemStatus = $item->user_id ? 'borrowed' : 'available';

            // Update status item kembali ke status sebelumnya
            $item->update(['status' => $newItemStatus]);

            // Update status missing_tools menjadi cancelled
            $missingTool->update([
                'status' => 'cancelled',
                'reclaimed_at' => now() // gunakan kolom yang sama untuk tracking kapan di-cancel
            ]);

            // Sync koin untuk user jika ada
            if ($item->user_id) {
                $userDetail = UserDetail::where('user_id', $item->user_id)->first();
                if ($userDetail) {
                    $userDetail->syncKoin();
                }
            }

            // Commit transaction dahulu
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Missing tool '{$missingTool->nama_barang}' has been successfully cancelled and item status restored to {$newItemStatus}",
                'data' => [
                    'missing_tool' => $missingTool->fresh(),
                    'item' => $item->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to cancel missing tool: ' . $e->getMessage(), [
                'missing_tool_id' => $missingToolId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling missing tool: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all missing tools
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMissingTools(Request $request)
    {
        try {
            $query = MissingTools::with(['item', 'user.detail']);

            // Filter berdasarkan status jika ada
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'reported_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $missingTools = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $missingTools
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get missing tools: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve missing tools data'
            ], 500);
        }
    }

    /**
     * Get detail of a missing tool
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Load missing tool dengan relasi yang diperlukan
            $missingTool = MissingTools::with([
                'item',
                'user',
                'user.detail',
                'userDetail' // Relasi langsung ke UserDetail
            ])->find($id);

            if (!$missingTool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing tool not found'
                ], 404);
            }

            // Debug untuk melihat data yang ter-load
            Log::info('Missing Tool Debug:', [
                'missing_tool_id' => $missingTool->id,
                'user_id' => $missingTool->user_id,
                'user_exists' => $missingTool->user ? 'Yes' : 'No',
                'user_detail_via_user' => $missingTool->user && $missingTool->user->detail ? 'Yes' : 'No',
                'user_detail_direct' => $missingTool->userDetail ? 'Yes' : 'No',
                'user_name_via_user' => $missingTool->user && $missingTool->user->detail ? $missingTool->user->detail->nama : 'N/A',
                'user_name_direct' => $missingTool->userDetail ? $missingTool->userDetail->nama : 'N/A'
            ]);

            // Generate HTML untuk modal
            $html = view('admin.missing-tools.detail-partial', compact('missingTool'))->render();

            return response()->json([
                'success' => true,
                'data' => $missingTool,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to show missing tool details: ' . $e->getMessage(), [
                'missing_tool_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load missing tool details'
            ], 500);
        }
    }
}
