<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RfidTag;
use App\Models\Item; // Add this import
use App\Models\ActivityLog; // Pastikan model ini ada
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== USER STATISTICS ==========
        $userCount = User::count();
        $lastWeekUserCount = User::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $userCountPercentage = $userCount > 0 && $lastWeekUserCount > 0
            ? '+' . round(($lastWeekUserCount / $userCount) * 100) . '%'
            : '0%';

        // ========== ACTIVE SESSIONS ==========
        // Menghitung user yang login dalam 24 jam terakhir
        $activeSessions = User::where('last_login_at', '>=', Carbon::now()->subDay())->count();
        $activeSessionsPercentage = $userCount > 0
            ? round(($activeSessions / $userCount) * 100) . '%'
            : '0%';

        // ========== RFID TAG STATISTICS ==========
        $rfidTagCount = RfidTag::count();
        $availableRfidTags = RfidTag::where('status', 'Available')->count();
        $usedRfidTags = RfidTag::where('status', 'Used')->count();
        $damagedRfidTags = RfidTag::where('status', 'Damaged')->count();

        // Menghitung persentase untuk setiap status RFID
        $availablePercentage = $rfidTagCount > 0
            ? round(($availableRfidTags / $rfidTagCount) * 100) . '%'
            : '0%';
        $usedPercentage = $rfidTagCount > 0
            ? round(($usedRfidTags / $rfidTagCount) * 100) . '%'
            : '0%';
        $damagedPercentage = $rfidTagCount > 0
            ? round(($damagedRfidTags / $rfidTagCount) * 100) . '%'
            : '0%';

        // ========== ITEM STATISTICS ==========
        $totalItems = Item::count();
        $availableItems = Item::available()->count(); // Using scope from Item model
        $outOfStockItems = Item::outOfStock()->count(); // Using scope from Item model
        $totalStock = Item::sum('available'); // Total quantity of all items

        // Item percentages
        $itemAvailabilityRate = $totalItems > 0
            ? round(($availableItems / $totalItems) * 100)
            : 0;

        // Recent items (added in last week)
        $recentItemsCount = Item::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $recentItemsPercentage = $totalItems > 0 && $recentItemsCount > 0
            ? '+' . round(($recentItemsCount / $totalItems) * 100) . '%'
            : '0%';

        // ========== SYSTEM STATUS ==========
        $systemStatus = 'Online'; // Bisa dibuat dinamis sesuai kebutuhan

        // ========== RECENT ACTIVITIES ==========
        // Jika Anda belum punya model ActivityLog, buat dulu atau gunakan data dummy
        $recentActivities = collect();

        // Cek apakah model ActivityLog ada
        if (class_exists('App\Models\ActivityLog')) {
            $recentActivities = ActivityLog::with('user')
                ->latest()
                ->limit(5)
                ->get();
        } else {
            // Data dummy untuk recent activities jika model belum ada
            $recentActivities = collect([
                (object) [
                    'description' => 'Admin logged into system',
                    'user' => (object) ['name' => 'Admin User'],
                    'created_at' => Carbon::now()->subMinutes(30)
                ],
                (object) [
                    'description' => 'New RFID tag added',
                    'user' => (object) ['name' => 'Admin User'],
                    'created_at' => Carbon::now()->subHours(2)
                ],
                (object) [
                    'description' => 'User profile updated',
                    'user' => (object) ['name' => 'John Doe'],
                    'created_at' => Carbon::now()->subHours(4)
                ],
                (object) [
                    'description' => 'New item added to inventory',
                    'user' => (object) ['name' => 'Admin User'],
                    'created_at' => Carbon::now()->subHours(6)
                ],
                (object) [
                    'description' => 'Item stock updated',
                    'user' => (object) ['name' => 'Staff User'],
                    'created_at' => Carbon::now()->subHours(8)
                ]
            ]);
        }

        // ========== WEEKLY STATISTICS FOR CHARTS ==========
        $weeklyUserRegistrations = [];
        $weeklyItemAdditions = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyUserRegistrations[] = [
                'date' => $date->format('Y-m-d'),
                'count' => User::whereDate('created_at', $date)->count()
            ];
            $weeklyItemAdditions[] = [
                'date' => $date->format('Y-m-d'),
                'count' => Item::whereDate('created_at', $date)->count()
            ];
        }

        return view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'content' => 'Management dashboard overview',

            // User Statistics
            'userCount' => $userCount,
            'userCountPercentage' => $userCountPercentage,

            // Active Sessions
            'activeSessions' => $activeSessions,
            'activeSessionsPercentage' => $activeSessionsPercentage,

            // RFID Tag Statistics
            'rfidTagCount' => $rfidTagCount,
            'availableRfidTags' => $availableRfidTags,
            'usedRfidTags' => $usedRfidTags,
            'damagedRfidTags' => $damagedRfidTags,
            'totalTags' => $rfidTagCount,

            // RFID Percentages
            'availablePercentage' => $availablePercentage,
            'usedPercentage' => $usedPercentage,
            'damagedPercentage' => $damagedPercentage,

            // Item Statistics
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'outOfStockItems' => $outOfStockItems,
            'totalStock' => $totalStock,
            'itemAvailabilityRate' => $itemAvailabilityRate,
            'recentItemsCount' => $recentItemsCount,
            'recentItemsPercentage' => $recentItemsPercentage,

            // System Status
            'systemStatus' => $systemStatus,

            // Recent Activities
            'recentActivities' => $recentActivities,

            // Weekly Statistics for Charts
            'weeklyUserRegistrations' => $weeklyUserRegistrations,
            'weeklyItemAdditions' => $weeklyItemAdditions,
        ]);
    }

    /**
     * Get dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $userCount = User::count();
        $rfidTagCount = RfidTag::count();
        $totalItems = Item::count();
        $availableItems = Item::available()->count();
        $outOfStockItems = Item::outOfStock()->count();
        $activeSessions = User::where('last_login_at', '>=', Carbon::now()->subDay())->count();

        return response()->json([
            'users' => $userCount,
            'rfidTags' => $rfidTagCount,
            'totalItems' => $totalItems,
            'availableItems' => $availableItems,
            'outOfStockItems' => $outOfStockItems,
            'activeSessions' => $activeSessions,
            'itemAvailabilityRate' => $totalItems > 0 ? round(($availableItems / $totalItems) * 100) : 0,
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Refresh dashboard data via AJAX
     */
    public function refresh()
    {
        // Method untuk refresh data dashboard jika dibutuhkan
        return response()->json([
            'success' => true,
            'message' => 'Dashboard data refreshed successfully',
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities()
    {
        $activities = collect();

        if (class_exists('App\Models\ActivityLog')) {
            $activities = ActivityLog::with('user')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'user' => $activity->user ? $activity->user->name : 'System',
                        'created_at' => $activity->created_at->diffForHumans(),
                        'type' => $activity->type ?? 'info'
                    ];
                });
        }

        return response()->json($activities);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'week'); // week, month, year
        $type = $request->get('type', 'users'); // users, items, activities

        $data = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $label = $date->format('M d');

                    if ($type === 'users') {
                        $count = User::whereDate('created_at', $date)->count();
                    } elseif ($type === 'items') {
                        $count = Item::whereDate('created_at', $date)->count();
                    } else {
                        $count = 0; // ActivityLog count if needed
                    }

                    $data[] = ['label' => $label, 'value' => $count];
                }
                break;

            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $label = $date->format('M d');

                    if ($type === 'users') {
                        $count = User::whereDate('created_at', $date)->count();
                    } elseif ($type === 'items') {
                        $count = Item::whereDate('created_at', $date)->count();
                    } else {
                        $count = 0;
                    }

                    $data[] = ['label' => $label, 'value' => $count];
                }
                break;
        }

        return response()->json($data);
    }
}
