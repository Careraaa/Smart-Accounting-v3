<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class DeleteOldNotifications extends Command
{
    protected $signature   = 'notifications:prune';
    protected $description = 'Delete notifications older than 30 days';

    public function handle(): void
    {
        $deleted = Notification::where('created_at', '<', now()->subDays(30))->delete();
        $this->info("Deleted {$deleted} old notification(s).");
    }
}
