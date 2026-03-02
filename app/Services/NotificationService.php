<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send a notification to a user
     */
    public function send(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultiple(array $userIds, string $type, string $title, string $message, array $data = []): Collection
    {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $notifications->push($this->send($user, $type, $title, $message, $data));
            }
        }

        return $notifications;
    }

    /**
     * Send notification to all users with a specific role
     */
    public function sendToRole(string $role, string $type, string $title, string $message, array $data = []): Collection
    {
        $users = User::where('role', $role)->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendToMultiple($userIds, $type, $title, $message, $data);
    }

    /**
     * Send notification to a department
     */
    public function sendToDepartment(string $department, string $type, string $title, string $message, array $data = []): Collection
    {
        $users = User::where('department', $department)->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendToMultiple($userIds, $type, $title, $message, $data);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(User $user, int $limit = null): Collection
    {
        $query = $user->notifications()->unread()->recent();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all notifications for a user
     */
    public function getAllNotifications(User $user, int $limit = null): Collection
    {
        $query = $user->notifications()->recent();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): Notification
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Notification $notification): Notification
    {
        return $notification->markAsUnread();
    }

    /**
     * Delete a notification
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Delete all read notifications for a user
     */
    public function deleteReadNotifications(User $user): int
    {
        return $user->notifications()->read()->delete();
    }

    /**
     * Delete all notifications for a user
     */
    public function deleteAllNotifications(User $user): int
    {
        return $user->notifications()->delete();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Get notification statistics for a user
     */
    public function getStats(User $user): array
    {
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->notifications()->unread()->count(),
            'read' => $user->notifications()->read()->count(),
        ];
    }
}
