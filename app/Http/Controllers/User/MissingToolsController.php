<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MissingTools;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MissingToolsController extends Controller
{
    /**
     * Display a listing of missing tools for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userDetail = UserDetail::where('user_id', $user->id)->first();

        return view('user.missing-tools.index', compact('userDetail', 'user'));
    }

    /**
     * Get data for DataTables - Only missing tools by current user
     */
    public function getData(Request $request)
    {
        try {
            $user = Auth::user();

            // Query untuk mendapatkan missing tools yang terkait dengan user ini
            $query = MissingTools::select([
                'id',
                'item_id',
                'epc',
                'nama_barang',
                'user_id',
                'status',
                'reported_at',
                'reclaimed_at',
                'created_at',
                'updated_at'
            ])
                ->where('user_id', $user->id)    // Hanya missing tools user ini
                ->with(['item', 'user']);        // Load relations

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('reported_at_formatted', function ($missingTool) {
                    return $missingTool->reported_at ? $missingTool->reported_at->format('d M Y, H:i') : '-';
                })
                ->addColumn('duration_days', function ($missingTool) {
                    return $missingTool->duration;
                })
                ->addColumn('duration_text', function ($missingTool) {
                    return $missingTool->duration_text;
                })
                ->addColumn('status_badge', function ($missingTool) {
                    return [
                        'class' => $missingTool->status_badge_class,
                        'text' => $missingTool->status_text
                    ];
                })
                ->addColumn('action_date', function ($missingTool) {
                    if ($missingTool->status === 'completed' && $missingTool->reclaimed_at) {
                        return $missingTool->reclaimed_at->format('d M Y, H:i');
                    } elseif ($missingTool->status === 'cancelled' && $missingTool->updated_at) {
                        return $missingTool->updated_at->format('d M Y, H:i');
                    }
                    return '-';
                })
                ->filter(function ($query) use ($request) {
                    // Status filter
                    if ($request->has('columns') && isset($request->columns[4]['search']['value'])) {
                        $statusFilter = $request->columns[4]['search']['value'];
                        if ($statusFilter && $statusFilter !== '') {
                            $query->where('status', $statusFilter);
                        }
                    }

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
            Log::error('Missing Tools DataTables Error: ' . $e->getMessage());

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
     * Get statistics data for current user's missing tools
     */
    private function getStatsData()
    {
        $user = Auth::user();

        return [
            'total_missing' => MissingTools::where('user_id', $user->id)->count(),
            'pending_missing' => MissingTools::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'reclaimed_missing' => MissingTools::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'cancelled_missing' => MissingTools::where('user_id', $user->id)
                ->where('status', 'cancelled')
                ->count(),
        ];
    }

    /**
     * Get missing tools statistics for the user.
     */
    public function getStats()
    {
        try {
            return response()->json([
                'success' => true,
                'stats' => $this->getStatsData()
            ]);
        } catch (\Exception $e) {
            Log::error('Get missing tools stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }

    /**
     * Search missing tools via AJAX.
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

            $missingTools = MissingTools::where('user_id', $user->id)
                ->where(function ($query) use ($search) {
                    $query->where('epc', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%");
                })
                ->with(['item', 'user'])
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $missingTools->map(function ($missingTool) {
                    return [
                        'id' => $missingTool->id,
                        'epc' => $missingTool->epc,
                        'nama_barang' => $missingTool->nama_barang,
                        'status' => $missingTool->status,
                        'status_text' => $missingTool->status_text,
                        'reported_at' => $missingTool->reported_at ? $missingTool->reported_at->format('d M Y H:i') : '-',
                        'duration' => $missingTool->duration_text
                    ];
                }),
                'count' => $missingTools->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Search missing tools error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to search missing tools'
            ], 500);
        }
    }
}
