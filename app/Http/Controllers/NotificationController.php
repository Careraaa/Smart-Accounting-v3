<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type'); // Optional filter by type
        $read = $request->query('read'); // 'read', 'unread', or empty for all

        $query = $user->notifications();

        if ($type) {
            $query->where('type', $type);
        }

        if ($read === 'unread') {
            $query->unread();
        } elseif ($read === 'read') {
            $query->read();
        }

        $notifications = $query->recent()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'stats' => $this->notificationService->getStats($user),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'status' => 'success',
            'unread_count' => $count,
        ]);
    }

    /**
     * Get a specific notification
     */
    public function show(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $notification,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification->refresh(),
        ]);
    }

    /**
     * Mark a notification as unread
     */
    public function markAsUnread(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->notificationService->markAsUnread($notification);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as unread',
            'data' => $notification->refresh(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read',
            'stats' => $this->notificationService->getStats($user),
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->notificationService->delete($notification);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteReadNotifications()
    {
        $user = Auth::user();
        $this->notificationService->deleteReadNotifications($user);

        return response()->json([
            'status' => 'success',
            'message' => 'All read notifications deleted',
            'stats' => $this->notificationService->getStats($user),
        ]);
    }

    /**
     * Delete all notifications
     */
    public function deleteAllNotifications()
    {
        $user = Auth::user();
        $this->notificationService->deleteAllNotifications($user);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications deleted',
            'stats' => $this->notificationService->getStats($user),
        ]);
    }

    /**
     * Get notification statistics
     */
    public function stats()
    {
        $user = Auth::user();
        $stats = $this->notificationService->getStats($user);

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }
}
