<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LogPeminjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LogPeminjamanController extends Controller
{
    public function index()
    {
        return view('user.log-peminjaman.index', [
            'title' => 'My Borrowing History',
            'content' => 'Your personal borrowing history'
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $userId = Auth::id();

            // Get logs only for current user
            $logs = LogPeminjaman::with(['user', 'item'])
                ->where('user_id', $userId)
                ->orderBy('timestamp', 'desc');

            // Apply date filter if provided
            if ($request->has('date') && !empty($request->get('date'))) {
                try {
                    $date = Carbon::parse($request->get('date'));
                    $logs = $logs->whereDate('timestamp', $date);
                } catch (Exception $e) {
                    // Invalid date format, ignore filter
                }
            }

            // Apply activity type filter if provided
            if ($request->has('activity_type') && !empty($request->get('activity_type'))) {
                $logs = $logs->where('activity_type', $request->get('activity_type'));
            }

            // Calculate user-specific statistics
            $stats = $this->calculateUserStats($userId);

            return DataTables::of($logs)
                ->addIndexColumn()
                ->addColumn('item_display', function ($log) {
                    if ($log->item && $log->item->nama_barang) {
                        return $log->item->nama_barang;
                    }
                    return $log->item_name ?? 'Unknown Item';
                })
                ->addColumn('timestamp_formatted', function ($log) {
                    try {
                        return Carbon::parse($log->timestamp)->format('d-m-Y H:i:s');
                    } catch (Exception $e) {
                        return '-';
                    }
                })
                ->addColumn('activity_badge', function ($log) {
                    if ($log->activity_type == 'pinjam') {
                        return '<span class="badge bg-primary"><i class="ti ti-arrow-up-right me-1"></i>Borrowed</span>';
                    } elseif ($log->activity_type == 'kembali') {
                        return '<span class="badge bg-success"><i class="ti ti-arrow-down-left me-1"></i>Returned</span>';
                    } else {
                        return '<span class="badge bg-secondary"><i class="ti ti-help me-1"></i>Unknown</span>';
                    }
                })
                ->addColumn('time_ago', function ($log) {
                    try {
                        $time = Carbon::parse($log->timestamp);
                        return '<span class="text-muted">' . $time->diffForHumans() . '</span>';
                    } catch (Exception $e) {
                        return '<span class="text-muted">-</span>';
                    }
                })
                ->addColumn('status', function ($log) {
                    if ($log->activity_type == 'pinjam') {
                        // Check if this item has been returned by this user
                        $hasReturned = LogPeminjaman::where('user_id', $log->user_id)
                            ->where('item_id', $log->item_id)
                            ->where('activity_type', 'kembali')
                            ->where('timestamp', '>', $log->timestamp)
                            ->exists();

                        if ($hasReturned) {
                            return '<span class="badge bg-success">Completed</span>';
                        } else {
                            return '<span class="badge bg-warning">Still Borrowed</span>';
                        }
                    }
                    return '<span class="badge bg-info">Returned</span>';
                })
                ->rawColumns(['activity_badge', 'time_ago', 'status'])
                ->with('stats', $stats)
                ->make(true);
        } catch (Exception $e) {
            Log::error('Error in User LogPeminjaman getData: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'stats' => [
                    'total_borrowed' => 0,
                    'total_returned' => 0,
                    'currently_borrowed' => 0,
                    'completion_rate' => 0
                ],
                'error' => 'An error occurred while fetching data'
            ]);
        }
    }

    public function getStats()
    {
        try {
            $userId = Auth::id();
            $stats = $this->calculateUserStats($userId);

            // Get user's activity trend over last 30 days
            $activityTrend = LogPeminjaman::select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('COUNT(*) as count'),
                'activity_type'
            )
                ->where('user_id', $userId)
                ->whereDate('timestamp', '>=', Carbon::now()->subDays(30))
                ->groupBy('date', 'activity_type')
                ->orderBy('date')
                ->get();

            // Get user's most borrowed items
            $mostBorrowedItems = LogPeminjaman::select('item_name', DB::raw('COUNT(*) as count'))
                ->where('user_id', $userId)
                ->where('activity_type', 'pinjam')
                ->whereNotNull('item_name')
                ->groupBy('item_name')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return response()->json([
                'stats' => $stats,
                'trend' => $activityTrend,
                'mostBorrowedItems' => $mostBorrowedItems
            ]);
        } catch (Exception $e) {
            Log::error('Error in User LogPeminjaman getStats: ' . $e->getMessage());

            return response()->json([
                'stats' => [
                    'total_borrowed' => 0,
                    'total_returned' => 0,
                    'currently_borrowed' => 0,
                    'completion_rate' => 0
                ],
                'trend' => [],
                'mostBorrowedItems' => []
            ]);
        }
    }

    public function getCurrentBorrowed()
    {
        try {
            $userId = Auth::id();

            // Get items currently borrowed by user
            $currentlyBorrowed = LogPeminjaman::with('item')
                ->select('item_id', 'item_name')
                ->selectRaw('MAX(timestamp) as last_activity')
                ->selectRaw('SUM(CASE WHEN activity_type = "pinjam" THEN 1 ELSE 0 END) as borrowed_count')
                ->selectRaw('SUM(CASE WHEN activity_type = "kembali" THEN 1 ELSE 0 END) as returned_count')
                ->where('user_id', $userId)
                ->groupBy('item_id', 'item_name')
                ->havingRaw('borrowed_count > returned_count')
                ->get()
                ->map(function ($log) {
                    return [
                        'item_name' => $log->item && $log->item->nama_barang ? $log->item->nama_barang : ($log->item_name ?? 'Unknown Item'),
                        'last_borrowed' => Carbon::parse($log->last_activity)->diffForHumans(),
                        'borrowed_date' => Carbon::parse($log->last_activity)->format('d-m-Y H:i:s')
                    ];
                });

            return response()->json([
                'currently_borrowed' => $currentlyBorrowed,
                'count' => $currentlyBorrowed->count()
            ]);
        } catch (Exception $e) {
            Log::error('Error in User LogPeminjaman getCurrentBorrowed: ' . $e->getMessage());

            return response()->json([
                'currently_borrowed' => [],
                'count' => 0
            ]);
        }
    }

    /**
     * Calculate user-specific statistics
     */
    private function calculateUserStats($userId)
    {
        try {
            $totalBorrowed = LogPeminjaman::where('user_id', $userId)
                ->where('activity_type', 'pinjam')
                ->count();

            $totalReturned = LogPeminjaman::where('user_id', $userId)
                ->where('activity_type', 'kembali')
                ->count();

            $currentlyBorrowed = $this->getUserCurrentlyBorrowedCount($userId);

            $completionRate = $totalBorrowed > 0 ? round(($totalReturned / $totalBorrowed) * 100, 1) : 0;

            return [
                'total_borrowed' => $totalBorrowed,
                'total_returned' => $totalReturned,
                'currently_borrowed' => $currentlyBorrowed,
                'completion_rate' => $completionRate
            ];
        } catch (Exception $e) {
            Log::error('Error calculating user stats: ' . $e->getMessage());

            return [
                'total_borrowed' => 0,
                'total_returned' => 0,
                'currently_borrowed' => 0,
                'completion_rate' => 0
            ];
        }
    }

    /**
     * Get count of currently borrowed items for specific user
     */
    private function getUserCurrentlyBorrowedCount($userId)
    {
        try {
            return LogPeminjaman::select('item_id')
                ->selectRaw('SUM(CASE WHEN activity_type = "pinjam" THEN 1 ELSE 0 END) as borrowed_count')
                ->selectRaw('SUM(CASE WHEN activity_type = "kembali" THEN 1 ELSE 0 END) as returned_count')
                ->where('user_id', $userId)
                ->groupBy('item_id')
                ->havingRaw('borrowed_count > returned_count')
                ->count();
        } catch (Exception $e) {
            Log::error('Error getting user currently borrowed count: ' . $e->getMessage());
            return 0;
        }
    }
}
