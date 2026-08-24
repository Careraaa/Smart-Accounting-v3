<?php

namespace App\Traits;

use App\Services\NotificationService;
use App\Models\User;

trait SendsNotifications
{
    /**
     * Send a notification to a user
     */
    protected function notify(User $user, string $type, string $title, string $message, array $data = [])
    {
        return app(NotificationService::class)->send($user, $type, $title, $message, $data);
    }

    /**
     * Send notification to multiple users
     */
    protected function notifyMultiple(array $userIds, string $type, string $title, string $message, array $data = [])
    {
        return app(NotificationService::class)->sendToMultiple($userIds, $type, $title, $message, $data);
    }

    /**
     * Send notification to all users with a specific role
     */
    protected function notifyRole(string $role, string $type, string $title, string $message, array $data = [])
    {
        return app(NotificationService::class)->sendToRole($role, $type, $title, $message, $data);
    }

    /**
     * Send notification to a department
     */
    protected function notifyDepartment(string $department, string $type, string $title, string $message, array $data = [])
    {
        return app(NotificationService::class)->sendToDepartment($department, $type, $title, $message, $data);
    }
}
