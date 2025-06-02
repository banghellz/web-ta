<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogPeminjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LogPeminjamanController extends Controller
{
    public function index()
    {
        return view('admin.log_peminjaman.index', [
            'title' => 'Borrowing History',
            'content' => 'Complete log of all borrowing activities'
        ]);
    }

    public function getData(Request $request)
    {
        try {
            // Get all logs without filtering
            $logs = LogPeminjaman::with(['user', 'item'])
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

            // Calculate statistics
            $stats = $this->calculateStats();

            return DataTables::of($logs)
                ->addIndexColumn()
                ->addColumn('user_name', function ($log) {
                    return $log->user ? $log->user->name : ($log->username ?? 'Unknown User');
                })
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
                ->rawColumns(['activity_badge', 'time_ago'])
                ->with('stats', $stats)
                ->make(true);
        } catch (Exception $e) {
            Log::error('Error in getData: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'stats' => [
                    'total_logs' => 0,
                    'currently_borrowed' => 0,
                    'total_returned' => 0,
                    'active_users' => 0
                ],
                'error' => 'An error occurred while fetching data'
            ]);
        }
    }

    public function clear()
    {
        try {
            DB::beginTransaction();

            $deletedCount = LogPeminjaman::count();
            LogPeminjaman::truncate();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deletedCount} borrowing logs"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error clearing logs: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error clearing logs. Please try again.'
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $stats = $this->calculateStats();

            // Get activity trend over last 7 days
            $activityTrend = LogPeminjaman::select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('COUNT(*) as count'),
                'activity_type'
            )
                ->whereDate('timestamp', '>=', Carbon::now()->subDays(7))
                ->groupBy('date', 'activity_type')
                ->orderBy('date')
                ->get();

            // Get top borrowed items (all time)
            $topItems = LogPeminjaman::select('item_name', DB::raw('COUNT(*) as count'))
                ->where('activity_type', 'pinjam')
                ->whereNotNull('item_name')
                ->groupBy('item_name')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            // Get most active users (all time)
            $topUsers = LogPeminjaman::select('username', DB::raw('COUNT(*) as count'))
                ->whereNotNull('username')
                ->groupBy('username')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return response()->json([
                'stats' => $stats,
                'trend' => $activityTrend,
                'topItems' => $topItems,
                'topUsers' => $topUsers
            ]);
        } catch (Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());

            return response()->json([
                'stats' => [
                    'total_logs' => 0,
                    'currently_borrowed' => 0,
                    'total_returned' => 0,
                    'active_users' => 0
                ],
                'trend' => [],
                'topItems' => [],
                'topUsers' => []
            ]);
        }
    }

    public function getBorrowedCount()
    {
        try {
            $totalBorrowed = $this->getCurrentlyBorrowedCount();

            return response()->json([
                'currently_borrowed_items' => $totalBorrowed
            ]);
        } catch (Exception $e) {
            Log::error('Error in getBorrowedCount: ' . $e->getMessage());

            return response()->json([
                'currently_borrowed_items' => 0
            ]);
        }
    }

    public function getSimpleData()
    {
        try {
            $logs = LogPeminjaman::with('item')
                ->orderBy('timestamp', 'desc')
                ->limit(100) // Limit to latest 100 records for performance
                ->get()
                ->map(function ($log) {
                    return [
                        'username' => $log->username,
                        'item_name' => $log->item && $log->item->nama_barang ? $log->item->nama_barang : ($log->item_name ?? 'Unknown Item'),
                        'activity_type' => ucfirst($log->activity_type),
                        'timestamp' => Carbon::parse($log->timestamp)->format('d-m-Y H:i:s'),
                        'time_ago' => Carbon::parse($log->timestamp)->diffForHumans()
                    ];
                });

            return response()->json([
                'currently_borrowed_items' => $this->getCurrentlyBorrowedCount(),
                'total_logs' => LogPeminjaman::count(),
                'logs' => $logs
            ]);
        } catch (Exception $e) {
            Log::error('Error in getSimpleData: ' . $e->getMessage());

            return response()->json([
                'currently_borrowed_items' => 0,
                'total_logs' => 0,
                'logs' => []
            ]);
        }
    }

    /**
     * Calculate comprehensive statistics
     */
    private function calculateStats()
    {
        try {
            $totalLogs = LogPeminjaman::count();
            $totalBorrowed = LogPeminjaman::where('activity_type', 'pinjam')->count();
            $totalReturned = LogPeminjaman::where('activity_type', 'kembali')->count();
            $currentlyBorrowed = $this->getCurrentlyBorrowedCount();
            $activeUsers = LogPeminjaman::distinct('user_id')->count('user_id');

            return [
                'total_logs' => $totalLogs,
                'total_borrowed' => $totalBorrowed,
                'total_returned' => $totalReturned,
                'currently_borrowed' => $currentlyBorrowed,
                'active_users' => $activeUsers
            ];
        } catch (Exception $e) {
            Log::error('Error calculating stats: ' . $e->getMessage());

            return [
                'total_logs' => 0,
                'total_borrowed' => 0,
                'total_returned' => 0,
                'currently_borrowed' => 0,
                'active_users' => 0
            ];
        }
    }

    /**
     * Get count of currently borrowed items
     */
    private function getCurrentlyBorrowedCount()
    {
        try {
            return LogPeminjaman::select('item_id', 'user_id')
                ->selectRaw('SUM(CASE WHEN activity_type = "pinjam" THEN 1 ELSE 0 END) as borrowed_count')
                ->selectRaw('SUM(CASE WHEN activity_type = "kembali" THEN 1 ELSE 0 END) as returned_count')
                ->groupBy('item_id', 'user_id')
                ->havingRaw('borrowed_count > returned_count')
                ->count();
        } catch (Exception $e) {
            Log::error('Error getting currently borrowed count: ' . $e->getMessage());
            return 0;
        }
    }
}
