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
use Illuminate\Validation\ValidationException;
use Mockery\Matcher\Not;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    /**
     * Display a listing of the items (view-only for users).
     */
    public function index()
    {
        $totalItems = Item::count();
        $availableItems = Item::available()->count();
        $borrowedItems = Item::borrowed()->count();
        $missingItems = Item::missing()->count();

        return view('user.items.index', [
            'title' => 'Tool Stocks',
            'content' => 'View available tools and their current status',
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'borrowedItems' => $borrowedItems,
            'missingItems' => $missingItems,
        ]);
    }

    /**
     * Get items data for DataTables AJAX (user view - read-only)
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
            ])->with(['borrower:id,name']); // Load borrower relationship

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('borrower_name', function ($item) {
                    return $item->borrower ? $item->borrower->name : '';
                })
                ->addColumn('updated_at_formatted', function ($item) {
                    return $item->updated_at->format('d M Y, H:i');
                })
                ->filter(function ($query) use ($request) {
                    // Status filter
                    if ($request->has('columns') && isset($request->columns[3]['search']['value'])) {
                        $statusFilter = $request->columns[3]['search']['value'];

                        if ($statusFilter && $statusFilter !== '') {
                            $query->where('status', $statusFilter);
                        }
                    }

                    // Search in tool name and EPC
                    if ($request->has('search') && $request->search['value']) {
                        $searchValue = $request->search['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('nama_barang', 'like', "%{$searchValue}%")
                                ->orWhere('epc', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->with([
                    'stats' => [
                        'total_items' => Item::count(),
                        'available_items' => Item::available()->count(),
                        'borrowed_items' => Item::borrowed()->count(),
                        'missing_items' => Item::missing()->count(),
                    ],
                    'last_update' => now()->toISOString()
                ])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('User DataTables Error: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }
}
