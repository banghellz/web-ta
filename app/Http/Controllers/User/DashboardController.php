<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\LogPeminjaman;
use App\Models\Item;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pastikan user detail tersedia dan sync koin
        $userDetail = $user->detail;
        if ($userDetail) {
            $userDetail->syncKoin(); // Sync koin berdasarkan item yang dipinjam
        }

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
        $userDetail = $user->detail;

        // Get borrowed items from Items table
        $borrowedItems = Item::where('user_id', $user->id)->get();
        $borrowedToolsCount = $borrowedItems->count();

        // Calculate overdue items (borrowed more than 7 days ago)
        $overdueCount = 0;
        foreach ($borrowedItems as $item) {
            if (Carbon::parse($item->updated_at)->diffInDays(Carbon::now()) > 7) {
                $overdueCount++;
            }
        }

        // Get total borrowing history dari log
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
            'max_borrow' => 10,
            'borrowed_items_details' => $borrowedItems,
            // Koin info dari UserDetail
            'available_koin' => $userDetail ? $userDetail->koin : 10,
            'used_koin' => $borrowedToolsCount, // 1 koin per item
            'total_koin' => 10
        ];
    }

    /**
     * Get quick statistics for dashboard cards
     */
    private function getQuickStats($user)
    {
        $userDetail = $user->detail;

        // Get current borrowed tools dari Items table
        $currentBorrowedCount = Item::where('user_id', $user->id)->count();

        // Get overdue items
        $overdueItems = $this->getOverdueItemsCount($user);

        // Get koin info dari UserDetail model
        $totalKoin = 10;
        $availableKoin = $userDetail ? $userDetail->koin : $totalKoin;
        $usedKoin = $totalKoin - $availableKoin;

        // Calculate available borrowing slots
        $maxBorrow = 10;
        $availableSlots = $maxBorrow - $currentBorrowedCount;

        return [
            'tools' => $currentBorrowedCount,
            'overdue' => $overdueItems,
            'coins' => $availableKoin,
            'coins_used' => $usedKoin,
            'coins_total' => $totalKoin,
            'available_slots' => $availableSlots,
            'max_borrow' => $maxBorrow
        ];
    }

    /**
     * Get storage usage statistics
     */
    private function getStorageStats($user)
    {
        // Gunakan data dari Items table
        $used = Item::where('user_id', $user->id)->count();
        $total = 10; // Maksimal peminjaman
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

        // Get currently borrowed items dari Items table
        $borrowedItems = Item::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($borrowedItems as $item) {
            $borrowDate = Carbon::parse($item->updated_at);
            $daysBorrowed = $borrowDate->diffInDays(Carbon::now());

            // Overdue items (more than 7 days)
            if ($daysBorrowed > 7) {
                $reminders[] = [
                    'message' => 'Overdue: Return ' . $item->nama_barang,
                    'time' => 'borrowed ' . $borrowDate->diffForHumans(),
                    'type' => 'overdue',
                    'priority' => 'high',
                    'item_id' => $item->id
                ];
            }
            // Items borrowed for 5-7 days (reminder)
            elseif ($daysBorrowed >= 5) {
                $reminders[] = [
                    'message' => 'Reminder: Return ' . $item->nama_barang . ' soon',
                    'time' => 'borrowed ' . $borrowDate->diffForHumans(),
                    'type' => 'reminder',
                    'priority' => 'medium',
                    'item_id' => $item->id
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
     * Fixed to accept $user parameter and return proper activity data
     */
    private function getRecentActivities($user)
    {
        try {
            // Get recent activities from LogPeminjaman untuk user yang sedang login
            $activities = LogPeminjaman::where('user_id', $user->id)
                ->with(['user', 'item']) // Load relasi jika ada
                ->orderBy('timestamp', 'desc')
                ->limit(10)
                ->get();

            $formattedActivities = [];

            foreach ($activities as $log) {
                $activity = [
                    'type' => $log->activity_type, // 'pinjam' atau 'kembali'
                    'date' => Carbon::parse($log->timestamp)->format('M d'),
                    'time' => Carbon::parse($log->timestamp)->format('H:i'),
                    'timestamp' => $log->timestamp
                ];

                // Format message berdasarkan activity type
                if ($log->activity_type === 'pinjam') {
                    $activity['message'] = 'Borrowed: ' . ($log->nama_barang ?? 'Item');
                } elseif ($log->activity_type === 'kembali') {
                    $activity['message'] = 'Returned: ' . ($log->nama_barang ?? 'Item');
                } else {
                    $activity['message'] = 'Activity: ' . ($log->nama_barang ?? 'Item');
                }

                $formattedActivities[] = $activity;
            }

            return $formattedActivities;
        } catch (\Exception $e) {
            Log::error('Failed to get recent activities for user dashboard: ' . $e->getMessage());
            return []; // Return empty array jika error
        }
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
        $borrowedItems = Item::where('user_id', $user->id)->get();

        $overdueCount = 0;
        foreach ($borrowedItems as $item) {
            if (Carbon::parse($item->updated_at)->diffInDays(Carbon::now()) > 7) {
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
        $userDetail = $user->detail;

        // Sync koin terlebih dahulu
        if ($userDetail) {
            $userDetail->syncKoin();
        }

        $currentBorrowed = Item::where('user_id', $user->id)->count();

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
            'available_koin' => $userDetail ? $userDetail->koin : 10,
            'used_koin' => $currentBorrowed,
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Refresh dashboard data via AJAX
     */
    public function refresh()
    {
        $user = Auth::user();
        $userDetail = $user->detail;

        // Sync koin saat refresh
        if ($userDetail) {
            $userDetail->syncKoin();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data refreshed successfully',
            'available_koin' => $userDetail ? $userDetail->koin : 10,
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

    /**
     * Force sync koin untuk user tertentu (admin function)
     */
    public function syncKoin()
    {
        $user = Auth::user();
        $userDetail = $user->detail;

        if ($userDetail) {
            $userDetail->syncKoin();

            return response()->json([
                'success' => true,
                'message' => 'Koin berhasil disinkronisasi',
                'available_koin' => $userDetail->koin,
                'borrowed_items' => $userDetail->borrowed_items_count
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User detail tidak ditemukan'
        ], 404);
    }
}
