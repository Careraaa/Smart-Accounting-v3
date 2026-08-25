<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Mark the previous completed workday after the day has ended.
        $schedule->command('attendance:mark-absent')
            ->dailyAt('00:05');

        // Delete attendance logs older than 2 days
        $schedule->command('attendance:delete-old-logs')
            ->daily()
            ->at('02:00'); // Run at 2 AM daily to check and delete old logs

        // Delete notifications older than 30 days
        $schedule->command('notifications:prune')
            ->daily()
            ->at('02:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
