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
        $view = $request->route('view') ?? $request->query('view', 'all');
        $type = $request->query('type'); // Optional filter by type
        $limit = $request->integer('limit'); // optional per-page / limit

        $query = $user->notifications();

        if ($view === 'deleted') {
            $query->deleted();
        } else {
            $query->notDeleted();
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($view === 'unread') {
            $query->unread();
        } elseif ($view === 'read') {
            $query->read();
        }

        $perPage = $limit && $limit > 0 ? min($limit, 50) : 15;
        $notifications = $query->recent()->paginate($perPage);

        // If the request expects JSON (dropdown polling, API usage), return JSON.
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $notifications,
                'stats' => $this->notificationService->getStats($user),
            ]);
        }

        // Otherwise render the Notifications page.
        $stats = $this->notificationService->getStats($user);
        return view('notifications.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'filter' => [
                'type' => $type,
                'view' => $view,
            ],
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
