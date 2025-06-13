<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mockery\Matcher\Not;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        $totalItems = Item::count();
        $availableItems = Item::where('available', '>', 0)->count();
        $outOfStockItems = Item::where('available', '<=', 0)->count();

        return view('user.items.index', [
            'title' => 'Item Management',
            'content' => 'Kelola semua tools dalam sistem',
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'outOfStockItems' => $outOfStockItems,
        ]);
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        return view('user.items.create', [
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
            ],
            'available' => [
                'required',
                'integer',
                'min:0'
            ]
        ], [
            'epc.required' => 'EPC field is required.',
            'epc.unique' => 'This EPC already exists in the system.',
            'nama_barang.required' => 'Item name is required.',
            'available.required' => 'Available quantity is required.',
            'available.min' => 'Available quantity cannot be negative.'
        ]);

        try {
            DB::beginTransaction();

            $item = Item::create($validated);

            DB::commit();
            Notification::toolAdded($item, auth()->user());

            return redirect()
                ->route('user.items.index')
                ->with('success', 'Item added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to add item. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        return view('user.items.show', [
            'title' => 'Item Details',
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        return view('user.items.edit', [
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
            'available' => [
                'required',
                'integer',
                'min:0'
            ]
        ], [
            'epc.required' => 'EPC field is required.',
            'epc.unique' => 'This EPC already exists in the system.',
            'nama_barang.required' => 'Item name is required.',
            'available.required' => 'Available quantity is required.',
            'available.min' => 'Available quantity cannot be negative.'
        ]);

        try {
            DB::beginTransaction();

            $item->update($validated);

            DB::commit();

            return redirect()
                ->route('user.items.index')
                ->with('success', 'Item updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to update item. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        try {
            DB::beginTransaction();

            $itemName = $item->nama_barang;
            $item->delete();

            DB::commit();
            Notification::toolDeleted($item, auth()->user());

            return redirect()
                ->route('user.items.index')
                ->with('success', "Item '{$itemName}' deleted successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete item. Please try again.');
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
                'available',
                'created_at'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function ($item) {
                    if ($item->available > 0) {
                        return 'available';
                    } else {
                        return 'out_of_stock';
                    }
                })
                ->addColumn('created_at_formatted', function ($item) {
                    return $item->created_at->format('d M Y, H:i');
                })
                ->addColumn('actions', function ($item) {
                    $editUrl = route('user.items.edit', $item->id);
                    $deleteUrl = route('user.items.destroy', $item->id);

                    return '
                        <div class="btn-group" role="group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-primary">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="' . $deleteUrl . '" style="display: inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="button" class="btn btn-sm btn-danger delete-item" 
                                        data-item-name="' . e($item->nama_barang) . '">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->filter(function ($query) use ($request) {
                    // Status filter
                    if ($request->has('columns') && isset($request->columns[4]['search']['value'])) {
                        $statusFilter = $request->columns[4]['search']['value'];
                        if ($statusFilter === 'available') {
                            $query->where('available', '>', 0);
                        } elseif ($statusFilter === 'out_of_stock') {
                            $query->where('available', '<=', 0);
                        }
                    }
                })
                ->with([
                    'stats' => [
                        'total_items' => Item::count(),
                        'available_items' => Item::where('available', '>', 0)->count(),
                        'out_of_stock_items' => Item::where('available', '<=', 0)->count(),
                        'availability_rate' => Item::count() > 0 ?
                            (Item::where('available', '>', 0)->count() / Item::count()) * 100 : 0
                    ]
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
     * Update item quantity (for borrowing/returning)
     */
    public function updateQuantity(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'action' => 'required|in:add,subtract'
        ]);

        try {
            DB::beginTransaction();

            if ($validated['action'] === 'add') {
                $item->increment('available', $validated['quantity']);
            } else {
                if ($item->available < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Not enough items available.'
                    ]);
                }
                $item->decrement('available', $validated['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item quantity updated successfully.',
                'new_quantity' => $item->fresh()->available
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update item quantity.'
            ], 422);
        }
    }
}
