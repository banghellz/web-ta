<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get latest notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $limit = min($limit, 50); // Max 50 notifications per request

            $notifications = Notification::with('user:id,name,email')
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data,
                        'user' => $notification->user ? [
                            'id' => $notification->user->id,
                            'name' => $notification->user->name,
                            'email' => $notification->user->email,
                        ] : null,
                        'time_ago' => $notification->time_ago,
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        'formatted_date' => $notification->created_at->format('M d, Y'),
                        'formatted_time' => $notification->created_at->format('H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'notifications' => []
            ], 500);
        }
    }

    /**
     * Get total notification count
     */
    public function getCount(): JsonResponse
    {
        try {
            $count = Notification::count();

            return response()->json([
                'success' => true,
                'total_count' => $count,
                'has_notifications' => $count > 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notification count: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'total_count' => 0,
                'has_notifications' => false
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Notification::count(),
                'today' => Notification::whereDate('created_at', today())->count(),
                'this_week' => Notification::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'by_type' => Notification::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notification stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'stats' => []
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Clear all notifications
     */
    public function clearAll(): JsonResponse
    {
        try {
            $deletedCount = Notification::count();
            Notification::truncate();

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deletedCount} notifications",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing all notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications'
            ], 500);
        }
    }

    /**
     * Mark notification as read (if you want to add read/unread functionality)
     */
    public function markAsRead($id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            // If you add a 'read_at' column to notifications table
            $notification->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Get notifications by type
     */
    public function getByType(Request $request, $type): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);

            $notifications = Notification::with('user:id,name,email')
                ->where('type', $type)
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data,
                        'user' => $notification->user,
                        'time_ago' => $notification->time_ago,
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'type' => $type,
                'total' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notifications by type: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'notifications' => []
            ], 500);
        }
    }
}
