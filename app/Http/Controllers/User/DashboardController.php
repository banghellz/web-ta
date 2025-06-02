<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LogPeminjaman;
use App\Models\Item;
use App\Models\ActivityLog; // Add this import if available
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ========== USER BORROWING STATISTICS ==========
        $userStats = $this->getUserBorrowingStats($user);

        // ========== QUICK STATS ==========
        $quickStats = $this->getQuickStats($user);

        // ========== STORAGE USAGE STATS ==========
        $storageStats = $this->getStorageStats($user);

        // ========== REMINDERS ==========
        $reminders = $this->getReminders($user);

        // ========== RECENT ACTIVITIES ==========
        $recentActivities = $this->getRecentActivities($user);

        // ========== MONTHLY BORROWING ACTIVITY ==========
        $monthlyActivity = $this->getMonthlyBorrowingActivity($user);

        // ========== WEEKLY BORROWING DATA FOR CHARTS ==========
        $weeklyBorrowingData = $this->getWeeklyBorrowingData($user);

        // ========== BORROWING TREND ANALYSIS ==========
        $borrowingTrend = $this->getBorrowingTrend($user);

        return view('user.dashboard.index', [
            'title' => 'User Dashboard',
            'pageTitle' => 'Dashboard',
            'content' => 'Personal dashboard overview',

            // User Statistics
            'userStats' => $userStats,
            'quickStats' => $quickStats,
            'storageStats' => $storageStats,

            // Activities & Reminders
            'reminders' => $reminders,
            'recentActivities' => $recentActivities,

            // Chart Data
            'monthlyActivity' => $monthlyActivity,
            'weeklyBorrowingData' => $weeklyBorrowingData,
            'borrowingTrend' => $borrowingTrend
        ]);
    }

    /**
     * Get comprehensive user borrowing statistics
     */
    private function getUserBorrowingStats($user)
    {
        // Get currently borrowed tools (tools that have been borrowed but not returned)
        $borrowedItems = DB::table('log_peminjaman as lp1')
            ->select('item_id', 'item_name', 'timestamp')
            ->where('user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('activity_type', 'pinjam')
            ->distinct()
            ->get();

        $borrowedToolsCount = $borrowedItems->count();

        // Calculate overdue items (borrowed more than 7 days ago)
        $overdueCount = 0;
        foreach ($borrowedItems as $item) {
            if (Carbon::parse($item->timestamp)->diffInDays(Carbon::now()) > 7) {
                $overdueCount++;
            }
        }

        // Get total borrowing history
        $totalBorrowed = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->count();

        // Get borrowing history for last week
        $lastWeekBorrowed = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->where('timestamp', '>=', Carbon::now()->subWeek())
            ->count();

        // Calculate borrowing trend percentage
        $borrowingTrendPercentage = $totalBorrowed > 0 && $lastWeekBorrowed > 0
            ? '+' . round(($lastWeekBorrowed / $totalBorrowed) * 100) . '%'
            : '0%';

        // Get returned items count
        $returnedItems = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'kembali')
            ->count();

        // Calculate return rate
        $returnRate = $totalBorrowed > 0
            ? round(($returnedItems / $totalBorrowed) * 100)
            : 0;

        return [
            'borrowed_tools' => $borrowedToolsCount,
            'overdue_count' => $overdueCount,
            'total_borrowed' => $totalBorrowed,
            'returned_items' => $returnedItems,
            'return_rate' => $returnRate,
            'borrowing_trend_percentage' => $borrowingTrendPercentage,
            'max_borrow' => 10, // Can be made configurable
            'borrowed_items_details' => $borrowedItems
        ];
    }

    /**
     * Get quick statistics for dashboard cards
     */
    private function getQuickStats($user)
    {
        // Get current borrowed tools count
        $currentBorrowedCount = DB::table('log_peminjaman as lp1')
            ->where('user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('activity_type', 'pinjam')
            ->distinct('item_id')
            ->count();

        // Get overdue items
        $overdueItems = $this->getOverdueItemsCount($user);

        // Get user coins (if coin system exists)
        $coins = $user->detail->koin ?? 7;
        $coinsUsed = $currentBorrowedCount * 1; // 1 coin per borrowed item

        // Calculate available borrowing slots
        $maxBorrow = 10; // Can be configurable
        $availableSlots = $maxBorrow - $currentBorrowedCount;

        return [
            'tools' => $currentBorrowedCount,
            'overdue' => $overdueItems,
            'coins' => $coins,
            'coins_used' => $coinsUsed,
            'available_slots' => $availableSlots,
            'max_borrow' => $maxBorrow
        ];
    }

    /**
     * Get storage usage statistics
     */
    private function getStorageStats($user)
    {
        $used = DB::table('log_peminjaman as lp1')
            ->where('user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('activity_type', 'pinjam')
            ->distinct('item_id')
            ->count();

        $total = 10; // Can be made configurable based on user level/plan
        $percentage = $total > 0 ? round(($used / $total) * 100) : 0;

        return [
            'used' => $used,
            'total' => $total,
            'percentage' => $percentage,
            'available' => $total - $used
        ];
    }

    /**
     * Get user reminders for overdue and upcoming returns
     */
    private function getReminders($user)
    {
        $reminders = [];

        // Get currently borrowed items
        $borrowedItems = DB::table('log_peminjaman as lp1')
            ->select('lp1.item_id', 'lp1.item_name', 'lp1.timestamp')
            ->where('lp1.user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('lp1.activity_type', 'pinjam')
            ->orderBy('lp1.timestamp', 'desc')
            ->get();

        foreach ($borrowedItems as $item) {
            $borrowDate = Carbon::parse($item->timestamp);
            $daysBorrowed = $borrowDate->diffInDays(Carbon::now());

            // Overdue items (more than 7 days)
            if ($daysBorrowed > 7) {
                $reminders[] = [
                    'message' => 'Overdue: Return ' . $item->item_name,
                    'time' => 'borrowed ' . $borrowDate->diffForHumans(),
                    'type' => 'overdue',
                    'priority' => 'high',
                    'item_id' => $item->item_id
                ];
            }
            // Items borrowed for 5-7 days (reminder)
            elseif ($daysBorrowed >= 5) {
                $reminders[] = [
                    'message' => 'Reminder: Return ' . $item->item_name . ' soon',
                    'time' => 'borrowed ' . $borrowDate->diffForHumans(),
                    'type' => 'reminder',
                    'priority' => 'medium',
                    'item_id' => $item->item_id
                ];
            }
        }

        // Sort by priority (high first)
        usort($reminders, function ($a, $b) {
            $priorities = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priorities[$b['priority']] - $priorities[$a['priority']];
        });

        return array_slice($reminders, 0, 5); // Return max 5 reminders
    }

    /**
     * Get recent activities with better formatting
     */
    private function getRecentActivities($user)
    {
        $activities = [];

        // Get recent log activities
        $recentLogs = LogPeminjaman::where('user_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();

        foreach ($recentLogs as $log) {
            $timestamp = Carbon::parse($log->timestamp);

            $activity = [
                'item_id' => $log->item_id,
                'item_name' => $log->item_name,
                'time' => $timestamp->format('H:i'),
                'date' => $timestamp->format('M d'),
                'full_date' => $timestamp->format('Y-m-d H:i:s'),
                'human_time' => $timestamp->diffForHumans()
            ];

            if ($log->activity_type == 'pinjam') {
                $activity['type'] = 'borrow';
                $activity['message'] = 'You borrowed ' . $log->item_name;
                $activity['icon'] = 'ti ti-download';
                $activity['color'] = 'success';
            } elseif ($log->activity_type == 'kembali') {
                $activity['type'] = 'return';
                $activity['message'] = 'You returned ' . $log->item_name;
                $activity['icon'] = 'ti ti-upload';
                $activity['color'] = 'info';
            }

            $activities[] = $activity;
        }

        return array_slice($activities, 0, 5); // Return only 5 most recent
    }

    /**
     * Get monthly borrowing activity data
     */
    private function getMonthlyBorrowingActivity($user)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        $monthlyData = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->whereRaw("DATE_FORMAT(timestamp, '%Y-%m') = ?", [$currentMonth])
            ->selectRaw('DATE(timestamp) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $monthlyData;
    }

    /**
     * Get weekly borrowing data for charts
     */
    private function getWeeklyBorrowingData($user)
    {
        $weeklyBorrowingData = [];
        $weeklyReturnData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $borrowCount = LogPeminjaman::where('user_id', $user->id)
                ->where('activity_type', 'pinjam')
                ->whereDate('timestamp', $date)
                ->count();

            $returnCount = LogPeminjaman::where('user_id', $user->id)
                ->where('activity_type', 'kembali')
                ->whereDate('timestamp', $date)
                ->count();

            $weeklyBorrowingData[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'borrow_count' => $borrowCount,
                'return_count' => $returnCount
            ];
        }

        return $weeklyBorrowingData;
    }

    /**
     * Get borrowing trend analysis
     */
    private function getBorrowingTrend($user)
    {
        $thisWeek = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->where('timestamp', '>=', Carbon::now()->startOfWeek())
            ->count();

        $lastWeek = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->whereBetween('timestamp', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])
            ->count();

        $trend = 'stable';
        $percentage = 0;

        if ($lastWeek > 0) {
            $percentage = round((($thisWeek - $lastWeek) / $lastWeek) * 100);
            if ($percentage > 0) {
                $trend = 'increasing';
            } elseif ($percentage < 0) {
                $trend = 'decreasing';
            }
        } elseif ($thisWeek > 0) {
            $trend = 'increasing';
            $percentage = 100;
        }

        return [
            'trend' => $trend,
            'percentage' => abs($percentage),
            'this_week' => $thisWeek,
            'last_week' => $lastWeek
        ];
    }

    /**
     * Get overdue items count
     */
    private function getOverdueItemsCount($user)
    {
        $borrowedItems = DB::table('log_peminjaman as lp1')
            ->select('timestamp')
            ->where('user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('activity_type', 'pinjam')
            ->get();

        $overdueCount = 0;
        foreach ($borrowedItems as $item) {
            if (Carbon::parse($item->timestamp)->diffInDays(Carbon::now()) > 7) {
                $overdueCount++;
            }
        }

        return $overdueCount;
    }

    /**
     * Get dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $user = Auth::user();

        $currentBorrowed = DB::table('log_peminjaman as lp1')
            ->where('user_id', $user->id)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('log_peminjaman as lp2')
                    ->whereRaw('lp2.item_id = lp1.item_id')
                    ->where('lp2.user_id', $user->id)
                    ->where('lp2.activity_type', 'kembali')
                    ->whereRaw('lp2.timestamp > lp1.timestamp');
            })
            ->where('activity_type', 'pinjam')
            ->distinct('item_id')
            ->count();

        $totalBorrowed = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'pinjam')
            ->count();

        $totalReturned = LogPeminjaman::where('user_id', $user->id)
            ->where('activity_type', 'kembali')
            ->count();

        $overdueCount = $this->getOverdueItemsCount($user);

        return response()->json([
            'current_borrowed' => $currentBorrowed,
            'total_borrowed' => $totalBorrowed,
            'total_returned' => $totalReturned,
            'overdue_count' => $overdueCount,
            'return_rate' => $totalBorrowed > 0 ? round(($totalReturned / $totalBorrowed) * 100) : 0,
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Refresh dashboard data via AJAX
     */
    public function refresh()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard data refreshed successfully',
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get recent activities for AJAX requests
     */
    public function getRecentActivitiesAjax()
    {
        $user = Auth::user();
        $activities = $this->getRecentActivities($user);

        return response()->json($activities);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'week'); // week, month
        $type = $request->get('type', 'borrowing'); // borrowing, returning

        $data = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $label = $date->format('M d');

                    if ($type === 'borrowing') {
                        $count = LogPeminjaman::where('user_id', $user->id)
                            ->where('activity_type', 'pinjam')
                            ->whereDate('timestamp', $date)
                            ->count();
                    } else {
                        $count = LogPeminjaman::where('user_id', $user->id)
                            ->where('activity_type', 'kembali')
                            ->whereDate('timestamp', $date)
                            ->count();
                    }

                    $data[] = ['label' => $label, 'value' => $count];
                }
                break;

            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $label = $date->format('M d');

                    if ($type === 'borrowing') {
                        $count = LogPeminjaman::where('user_id', $user->id)
                            ->where('activity_type', 'pinjam')
                            ->whereDate('timestamp', $date)
                            ->count();
                    } else {
                        $count = LogPeminjaman::where('user_id', $user->id)
                            ->where('activity_type', 'kembali')
                            ->whereDate('timestamp', $date)
                            ->count();
                    }

                    $data[] = ['label' => $label, 'value' => $count];
                }
                break;
        }

        return response()->json($data);
    }
}
