<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Leave notification types that accountants should never see.
     */
    private const LEAVE_TYPES = [
        'leave_submitted',
        'leave_approved',
        'leave_rejected',
        'leave_updated',
        'leave_deleted',
        'leave_pending_approval',
    ];

    public function send(Model $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
        ]);
    }

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

    public function sendToRole(string $role, string $type, string $title, string $message, array $data = []): Collection
    {
        $users   = User::where('role', $role)->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendToMultiple($userIds, $type, $title, $message, $data);
    }

    public function sendToDepartment(string $department, string $type, string $title, string $message, array $data = []): Collection
    {
        $users   = User::where('department', $department)->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendToMultiple($userIds, $type, $title, $message, $data);
    }

    public function getUnreadNotifications(Model $user, int $limit = null): Collection
    {
        $query = $user->notifications()->notDeleted()->unread()->recent();

        if ($user->role === 'accountant') {
            $query->whereNotIn('type', self::LEAVE_TYPES);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getAllNotifications(Model $user, int $limit = null): Collection
    {
        $query = $user->notifications()->notDeleted()->recent();

        if ($user->role === 'accountant') {
            $query->whereNotIn('type', self::LEAVE_TYPES);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function markAsRead(Notification $notification): Notification
    {
        return $notification->markAsRead();
    }

    public function markAllAsRead(Model $user): int
    {
        return $user->notifications()
            ->notDeleted()
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function markAsUnread(Notification $notification): Notification
    {
        return $notification->markAsUnread();
    }

    public function delete(Notification $notification): bool
    {
        $notification->markAsDeleted();
        return true;
    }

    public function deleteReadNotifications(Model $user): int
    {
        return $user->notifications()
            ->notDeleted()
            ->read()
            ->update(['deleted_at' => now()]);
    }

    public function deleteAllNotifications(Model $user): int
    {
        return $user->notifications()
            ->notDeleted()
            ->update(['deleted_at' => now()]);
    }

    public function getUnreadCount(Model $user): int
    {
        $query = $user->notifications()->notDeleted()->unread();

        if ($user->role === 'accountant') {
            $query->whereNotIn('type', self::LEAVE_TYPES);
        }

        return $query->count();
    }

    public function getStats(Model $user): array
    {
        $base = $user->notifications();

        if ($user->role === 'accountant') {
            $base->whereNotIn('type', self::LEAVE_TYPES);
        }

        return [
            'total'   => (clone $base)->notDeleted()->count(),
            'unread'  => (clone $base)->notDeleted()->unread()->count(),
            'read'    => (clone $base)->notDeleted()->read()->count(),
            'deleted' => (clone $base)->deleted()->count(),
        ];
    }
}