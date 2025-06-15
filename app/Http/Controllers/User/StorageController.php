<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class StorageController extends Controller
{
    /**
     * Display a listing of borrowed items by the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userDetail = UserDetail::where('user_id', $user->id)->first();

        // Sync koin untuk memastikan data terbaru
        if ($userDetail) {
            $userDetail->syncKoin();
        }

        return view('user.storage.index', compact('userDetail', 'user'));
    }

    /**
     * Get data for DataTables - Only borrowed items by current user
     */
    public function getData(Request $request)
    {
        try {
            $user = Auth::user();

            // Query untuk mendapatkan HANYA item yang sedang dipinjam oleh user ini
            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'created_at',
                'updated_at'
            ])
                ->where('user_id', $user->id)    // Hanya item yang dipinjam user ini
                ->where('status', 'borrowed');    // Hanya item dengan status borrowed

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('borrowed_at_formatted', function ($item) {
                    // Menggunakan updated_at sebagai tanggal peminjaman
                    return $item->updated_at ? $item->updated_at->format('d M Y, H:i') : '-';
                })
                ->addColumn('duration_days', function ($item) {
                    // Menghitung durasi dari updated_at (waktu peminjaman)
                    return $item->updated_at ? $item->updated_at->diffInDays(now()) : 0;
                })
                ->addColumn('status_text', function ($item) {
                    // Status selalu 'Borrowed' karena kita filter hanya borrowed items
                    return 'Borrowed';
                })
                ->filter(function ($query) use ($request) {
                    // Search in EPC and item name
                    if ($request->has('search') && $request->search['value']) {
                        $searchValue = $request->search['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('epc', 'like', "%{$searchValue}%")
                                ->orWhere('nama_barang', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->with([
                    'stats' => $this->getStatsData()
                ])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Storage DataTables Error: ' . $e->getMessage());

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
     * Get statistics data for current user
     */
    /**
     * Get statistics data for current user
     */
    private function getStatsData()
    {
        $user = Auth::user();

        return [
            // Currently borrowed items count
            'total_borrowed' => Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->count(),

            // Items borrowed today
            'borrowed_today' => Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->whereDate('updated_at', today())
                ->count(),

            // Items borrowed this week (matches borrowed_week in JS)
            'borrowed_week' => Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    /**
     * Show the form for showing item details (simplified view).
     */
    public function show($id)
    {
        $user = Auth::user();

        // Pastikan item yang ditampilkan adalah milik user dan sedang dipinjam
        $item = Item::where('user_id', $user->id)
            ->where('id', $id)
            ->where('status', 'borrowed')
            ->firstOrFail();

        return view('user.storage.show', compact('item'));
    }

    /**
     * Get storage statistics for the user.
     */
    public function getStats()
    {
        try {
            return response()->json([
                'success' => true,
                'stats' => $this->getStatsData()
            ]);
        } catch (\Exception $e) {
            Log::error('Get storage stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }

    /**
     * Search borrowed items via AJAX.
     */
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('q');

            if (!$search || strlen($search) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query must be at least 2 characters'
                ], 400);
            }

            $items = Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->where(function ($query) use ($search) {
                    $query->where('epc', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $items->map(function ($item) {
                    $daysBorrowed = $item->updated_at ? $item->updated_at->diffInDays(now()) : 0;

                    return [
                        'id' => $item->id,
                        'epc' => $item->epc,
                        'nama_barang' => $item->nama_barang,
                        'borrowed_at' => $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-',
                        'duration_days' => $daysBorrowed,
                        'url' => route('user.storage.show', $item->id)
                    ];
                }),
                'count' => $items->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Search borrowed items error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to search borrowed items'
            ], 500);
        }
    }

    /**
     * Export borrowed items to CSV.
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();

            $items = Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->orderBy('updated_at', 'desc')
                ->get();

            $filename = 'my_borrowed_items_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($items) {
                $file = fopen('php://output', 'w');

                // CSV Header
                fputcsv($file, [
                    'EPC Code',
                    'Tool Name',
                    'Borrowed Date',
                    'Duration (Days)',
                    'Status'
                ]);

                // CSV Data
                foreach ($items as $item) {
                    $borrowedDate = $item->updated_at;
                    $daysBorrowed = $borrowedDate ? $borrowedDate->diffInDays(now()) : 0;

                    fputcsv($file, [
                        $item->epc,
                        $item->nama_barang,
                        $borrowedDate ? $borrowedDate->format('d/m/Y H:i') : '-',
                        $daysBorrowed,
                        'Borrowed'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export borrowed items error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Get user's coin information
     */
    public function getCoinInfo()
    {
        try {
            $user = Auth::user();
            $userDetail = UserDetail::where('user_id', $user->id)->first();

            if ($userDetail) {
                $userDetail->syncKoin(); // Sync latest coin info
            }

            $currentBorrowed = Item::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->count();

            return response()->json([
                'success' => true,
                'available_coins' => $userDetail ? $userDetail->koin : 10,
                'used_coins' => $currentBorrowed,
                'total_coins' => 10,
                'current_borrowed' => $currentBorrowed
            ]);
        } catch (\Exception $e) {
            Log::error('Get coin info error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get coin information'
            ], 500);
        }
    }
}
