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

        return view('user.storage.index', compact('userDetail', 'user'));
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $user = Auth::user();

            // Query untuk mendapatkan item yang dipinjam oleh user ini
            $query = Item::select([
                'id',
                'epc',
                'nama_barang',
                'available',
                'user_id',
                'created_at',
                'updated_at'
            ])
                ->where('user_id', $user->id); // Hanya item yang dipinjam user ini

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('borrowed_at_formatted', function ($item) {
                    // Menggunakan updated_at sebagai tanggal peminjaman
                    return $item->updated_at ? $item->updated_at->format('d M Y, H:i') : '-';
                })
                ->addColumn('duration_days', function ($item) {
                    // Menghitung durasi dari updated_at (waktu peminjaman) dan bulatkan ke bawah
                    return $item->updated_at ? floor($item->updated_at->diffInDays(now())) : 0;
                })
                ->addColumn('status', function ($item) {
                    // Menentukan status berdasarkan durasi peminjaman
                    $daysBorrowed = $item->updated_at ? $item->updated_at->diffInDays(now()) : 0;

                    if ($daysBorrowed > 30) {
                        return 'overdue';
                    }

                    return 'Borrowed'; // Status default untuk item yang dipinjam
                })
                ->addColumn('actions', function ($item) {
                    $showUrl = route('user.storage.show', $item->id);

                    return '
                        <div class="btn-group" role="group">
                            <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    // Filter berdasarkan pencarian global saja
                })
                ->with([
                    'stats' => $this->getStatsData()
                ])
                ->rawColumns([])
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
     * Get statistics data
     */
    private function getStatsData()
    {
        $user = Auth::user();

        return [
            'total_borrowed' => Item::where('user_id', $user->id)->count(),
            'borrowed_today' => Item::where('user_id', $user->id)
                ->whereDate('updated_at', today())
                ->count(),
            'borrowed_this_week' => Item::where('user_id', $user->id)
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'borrowed_this_month' => Item::where('user_id', $user->id)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Show the form for showing item details.
     */
    public function show($id)
    {
        $user = Auth::user();

        $item = Item::where('user_id', $user->id)
            ->where('id', $id)
            ->with(['borrower', 'borrowerDetail'])
            ->firstOrFail();

        return view('user.storage.show', compact('item'));
    }

    /**
     * Get storage statistics for the user.
     */
    public function getStats()
    {
        return response()->json($this->getStatsData());
    }

    /**
     * Search borrowed items via AJAX.
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('q');

        $items = Item::where('user_id', $user->id)
            ->where(function ($query) use ($search) {
                $query->where('epc', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%");
            })
            ->with(['borrower', 'borrowerDetail'])
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items->map(function ($item) {
                $daysBorrowed = $item->updated_at ? floor($item->updated_at->diffInDays(now())) : 0;

                return [
                    'id' => $item->id,
                    'epc' => $item->epc,
                    'nama_barang' => $item->nama_barang,
                    'borrowed_at' => $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-',
                    'duration_days' => $daysBorrowed,
                    'url' => route('user.storage.show', $item->id)
                ];
            })
        ]);
    }

    /**
     * Export borrowed items to CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $items = Item::where('user_id', $user->id)
            ->with(['borrower', 'borrowerDetail'])
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
                'EPC',
                'Nama Barang',
                'Tanggal Pinjam',
                'Lama Pinjam (Hari)'
            ]);

            // CSV Data
            foreach ($items as $item) {
                $borrowedDate = $item->updated_at;
                $daysBorrowed = $borrowedDate ? floor($borrowedDate->diffInDays(now())) : 0;

                fputcsv($file, [
                    $item->epc,
                    $item->nama_barang,
                    $borrowedDate ? $borrowedDate->format('d/m/Y H:i') : '-',
                    $daysBorrowed
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
