<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NotificationService
{
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
        $query = $user->notifications()->unread()->recent();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getAllNotifications(Model $user, int $limit = null): Collection
    {
        $query = $user->notifications()->recent();

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
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function markAsUnread(Notification $notification): Notification
    {
        return $notification->markAsUnread();
    }

    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    public function deleteReadNotifications(Model $user): int
    {
        return $user->notifications()->read()->delete();
    }

    public function deleteAllNotifications(Model $user): int
    {
        return $user->notifications()->delete();
    }

    public function getUnreadCount(Model $user): int
    {
        return $user->notifications()->unread()->count();
    }

    public function getStats(Model $user): array
    {
        return [
            'total'  => $user->notifications()->count(),
            'unread' => $user->notifications()->unread()->count(),
            'read'   => $user->notifications()->read()->count(),
        ];
    }
}