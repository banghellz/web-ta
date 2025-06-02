<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('admin.activity_logs.index', [
            'title' => 'User Activity Logs',
            'content' => 'Monitor user login activities'
        ]);
    }

    public function getData()
    {
        $logs = ActivityLog::with('user');

        // Get statistics for today
        $today = Carbon::today();
        $stats = [
            'login' => ActivityLog::where('activity', 'login')
                ->whereDate('created_at', $today)
                ->count(),
            'register' => ActivityLog::where('activity', 'register')
                ->whereDate('created_at', $today)
                ->count(),
            'logout' => ActivityLog::where('activity', 'logout')
                ->whereDate('created_at', $today)
                ->count(),
            'uniqueUsers' => ActivityLog::whereDate('created_at', $today)
                ->distinct('user_id')
                ->count('user_id')
        ];

        // Filter by date if provided
        if (request()->has('date')) {
            $date = Carbon::parse(request('date'));
            $logs = $logs->whereDate('created_at', $date);
        }

        $datatables = DataTables::of($logs)
            ->addIndexColumn()
            ->addColumn('user_name', function ($log) {
                return $log->user ? $log->user->name : 'Unknown User';
            })
            ->addColumn('user_email', function ($log) {
                return $log->user ? $log->user->email : 'N/A';
            })
            ->addColumn('user_role', function ($log) {
                return $log->user ? $log->user->role : 'N/A';
            })
            ->addColumn('created_at_formatted', function ($log) {
                return $log->created_at->format('d-m-Y H:i:s');
            })
            ->with('stats', $stats);

        return $datatables->toJson();
    }

    public function clear()
    {
        // Add security check here if needed
        ActivityLog::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Activity logs cleared successfully'
        ]);
    }

    public function getStats()
    {
        $today = Carbon::today();

        // Get counts by activity type for today
        $stats = [
            'login' => ActivityLog::where('activity', 'login')
                ->whereDate('created_at', $today)
                ->count(),
            'register' => ActivityLog::where('activity', 'register')
                ->whereDate('created_at', $today)
                ->count(),
            'logout' => ActivityLog::where('activity', 'logout')
                ->whereDate('created_at', $today)
                ->count(),
            'uniqueUsers' => ActivityLog::whereDate('created_at', $today)
                ->distinct('user_id')
                ->count('user_id')
        ];

        // Get activity over time (last 7 days)
        $activityTrend = ActivityLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            'activity'
        )
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date', 'activity')
            ->get()
            ->groupBy('date');

        return response()->json([
            'stats' => $stats,
            'trend' => $activityTrend
        ]);
    }
}
