<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RfidTag;
use App\Models\Item;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        try {
            // Cache dashboard data for 5 minutes to improve performance
            $dashboardData = Cache::remember('dashboard_data', 60, function () {
                return $this->getDashboardData();
            });

            return view('admin.dashboard.index', $dashboardData);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());

            return view('admin.dashboard.index', [
                'title' => 'Master Admin Dashboard',
                'content' => 'Management dashboard overview',
                'error' => 'Unable to load dashboard data'
            ]);
        }
    }

    /**
     * Get all dashboard data
     */
    private function getDashboardData()
    {
        // ========== USER STATISTICS ==========
        $userCount = User::count();
        $lastWeekUserCount = User::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $userCountPercentage = $this->calculatePercentage($lastWeekUserCount, $userCount, true);

        // ========== TOOLS STATISTICS (FIXED LOGIC) ==========
        // PERBAIKAN: Gunakan field 'status' bukan 'available'

        // Total tools (semua item dalam sistem kecuali yang missing)
        $totalTools = Item::where('status', '!=', 'missing')->count();

        // Available tools: status = 'available'
        $availableTools = Item::where('status', 'available')->count();

        // Borrowed tools: status = 'borrowed'
        $borrowedTools = Item::where('status', 'borrowed')->count();

        // Missing tools: status = 'missing'
        $missingTools = Item::where('status', 'missing')->count();

        // Out of stock tools: status = 'out_of_stock'
        $outOfStockTools = Item::where('status', 'out_of_stock')->count();

        // Calculate tool percentages
        $toolAvailabilityRate = $this->calculatePercentage($availableTools, $totalTools);
        $borrowedToolsPercentage = $this->calculatePercentage($borrowedTools, $totalTools, true);
        $missingToolsPercentage = $this->calculatePercentage($missingTools, $totalTools, true);
        $outOfStockPercentage = $this->calculatePercentage($outOfStockTools, $totalTools, true);

        // Recent tools (added in last week)
        $recentToolsCount = Item::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $recentToolsPercentage = $this->calculatePercentage($recentToolsCount, $totalTools, true);

        // ========== RFID TAG STATISTICS ==========
        $rfidTagCount = RfidTag::count();
        $availableRfidTags = RfidTag::where('status', 'Available')->count();
        $usedRfidTags = RfidTag::where('status', 'Used')->count();
        $damagedRfidTags = RfidTag::where('status', 'Damaged')->count();

        // Calculate RFID percentages
        $availableRfidPercentage = $this->calculatePercentage($availableRfidTags, $rfidTagCount, true);
        $usedRfidPercentage = $this->calculatePercentage($usedRfidTags, $rfidTagCount, true);
        $damagedRfidPercentage = $this->calculatePercentage($damagedRfidTags, $rfidTagCount, true);

        // ========== SYSTEM STATUS ==========
        $systemStatus = $this->getSystemStatus();

        // ========== RECENT ACTIVITIES ==========
        $recentActivities = $this->getRecentActivitiesData();

        // ========== WEEKLY STATISTICS FOR CHARTS ==========
        $weeklyStats = $this->getWeeklyStatistics();

        // ========== DEBUG LOG ==========
        Log::info('Dashboard Data Debug', [
            'userCount' => $userCount,
            'totalTools' => $totalTools,
            'availableTools' => $availableTools,
            'borrowedTools' => $borrowedTools,
            'missingTools' => $missingTools,
            'rfidTagCount' => $rfidTagCount,
            'availableRfidTags' => $availableRfidTags
        ]);

        return [
            'title' => 'Master Admin Dashboard',
            'content' => 'Management dashboard overview',

            // User Statistics
            'userCount' => $userCount,
            'userCountPercentage' => $userCountPercentage,

            // Tools Statistics (FIXED)
            'totalTools' => $totalTools,
            'availableTools' => $availableTools,
            'borrowedTools' => $borrowedTools,
            'missingTools' => $missingTools,
            'outOfStockTools' => $outOfStockTools,
            'missingToolsPercentage' => $missingToolsPercentage,
            'toolAvailabilityRate' => $toolAvailabilityRate,
            'borrowedToolsPercentage' => $borrowedToolsPercentage,
            'outOfStockPercentage' => $outOfStockPercentage,
            'recentToolsCount' => $recentToolsCount,
            'recentToolsPercentage' => $recentToolsPercentage,

            // RFID Tag Statistics
            'rfidTagCount' => $rfidTagCount,
            'availableRfidTags' => $availableRfidTags,
            'usedRfidTags' => $usedRfidTags,
            'damagedRfidTags' => $damagedRfidTags,
            'availableRfidPercentage' => $availableRfidPercentage,
            'usedRfidPercentage' => $usedRfidPercentage,
            'damagedRfidPercentage' => $damagedRfidPercentage,

            // System Status
            'systemStatus' => $systemStatus,

            // Recent Activities
            'recentActivities' => $recentActivities,

            // Weekly Statistics
            'weeklyUserRegistrations' => $weeklyStats['users'],
            'weeklyToolAdditions' => $weeklyStats['tools'],
            'weeklyBorrowings' => $weeklyStats['borrowings'],
        ];
    }

    /**
     * Calculate percentage with optional plus sign
     */
    private function calculatePercentage($value, $total, $includeSign = false)
    {
        if ($total <= 0) {
            return $includeSign ? '+0%' : '0%';
        }

        $percentage = round(($value / $total) * 100);
        return $includeSign ? "+{$percentage}%" : "{$percentage}%";
    }

    /**
     * Get system status - SIMPLIFIED
     */
    private function getSystemStatus()
    {
        try {
            // Check database connection
            DB::connection()->getPdo();
            return 'Online';
        } catch (\Exception $e) {
            Log::error('System status check failed: ' . $e->getMessage());
            return 'Offline';
        }
    }

    /**
     * Get weekly statistics for charts - FIXED LOGIC
     */
    private function getWeeklyStatistics()
    {
        $weeklyUserRegistrations = [];
        $weeklyToolAdditions = [];
        $weeklyBorrowings = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $weeklyUserRegistrations[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'count' => User::whereDate('created_at', $date)->count()
            ];

            $weeklyToolAdditions[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'count' => Item::whereDate('created_at', $date)->count()
            ];

            // FIXED: Borrowings based on status = 'borrowed'
            $weeklyBorrowings[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'count' => Item::where('status', 'borrowed')
                    ->whereDate('updated_at', $date)
                    ->count()
            ];
        }

        return [
            'users' => $weeklyUserRegistrations,
            'tools' => $weeklyToolAdditions,
            'borrowings' => $weeklyBorrowings
        ];
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivitiesData()
    {
        try {
            if (class_exists('App\Models\ActivityLog')) {
                return ActivityLog::with('user')
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            return collect(); // Return empty collection if ActivityLog doesn't exist
        } catch (\Exception $e) {
            Log::error('Failed to get recent activities data: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Refresh dashboard data via AJAX
     */
    public function refresh()
    {
        try {
            // Clear dashboard cache
            Cache::forget('dashboard_data');

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data refreshed successfully',
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to refresh dashboard: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh dashboard data'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for AJAX requests - FIXED LOGIC
     */
    public function getStats()
    {
        try {
            // PERBAIKAN: Gunakan field 'status' bukan 'available'
            $totalTools = Item::count();
            $availableTools = Item::where('status', 'available')->count();
            $borrowedTools = Item::where('status', 'borrowed')->count();
            $missingTools = Item::where('status', 'missing')->count();
            $outOfStockTools = Item::where('status', 'out_of_stock')->count();

            $stats = [
                'users' => User::count(),
                'rfidTags' => RfidTag::count(),
                'totalTools' => $totalTools,
                'availableTools' => $availableTools,
                'borrowedTools' => $borrowedTools,
                'missingTools' => $missingTools,
                'outOfStockTools' => $outOfStockTools,
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            $stats['toolAvailabilityRate'] = $totalTools > 0
                ? round(($availableTools / $totalTools) * 100)
                : 0;

            $stats['borrowedToolsPercentage'] = $totalTools > 0
                ? round(($borrowedTools / $totalTools) * 100)
                : 0;

            $stats['missingToolsPercentage'] = $totalTools > 0
                ? round(($missingTools / $totalTools) * 100)
                : 0;

            // Debug log
            Log::info('Dashboard Stats Debug', $stats);

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Failed to get dashboard stats: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to retrieve statistics'
            ], 500);
        }
    }

    /**
     * Debug method untuk check data
     */
    public function debug()
    {
        try {
            $debugData = [
                'users_count' => User::count(),
                'items_total' => Item::count(),
                'items_by_status' => [
                    'available' => Item::where('status', 'available')->count(),
                    'borrowed' => Item::where('status', 'borrowed')->count(),
                    'missing' => Item::where('status', 'missing')->count(),
                    'out_of_stock' => Item::where('status', 'out_of_stock')->count(),
                ],
                'rfid_total' => RfidTag::count(),
                'rfid_by_status' => [
                    'Available' => RfidTag::where('status', 'Available')->count(),
                    'Used' => RfidTag::where('status', 'Used')->count(),
                    'Damaged' => RfidTag::where('status', 'Damaged')->count(),
                ],
                'sample_items' => Item::take(5)->get(['id', 'nama_barang', 'status', 'user_id']),
                'sample_users' => User::take(5)->get(['id', 'name', 'email']),
                'sample_rfid' => RfidTag::take(5)->get(['id', 'uid', 'status']),
            ];

            return response()->json($debugData, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
