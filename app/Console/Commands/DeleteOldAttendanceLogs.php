<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use Illuminate\Console\Command;

class DeleteOldAttendanceLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:delete-old-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old attendance logs (temporary QR scan records) older than 2 days. Keeps employee time_in and time_out records intact.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete only temporary AttendanceLog records older than 2 days
        // This keeps the actual Attendance table with employee time_in and time_out records
        $twoDaysAgo = now()->subDays(2);
        
        $deleted = AttendanceLog::where('logged_at', '<', $twoDaysAgo)->delete();
        
        $this->info("✓ Deleted {$deleted} old temporary attendance logs (QR scan records).");
        $this->info("✓ Employee time_in and time_out records in Attendance table are preserved.");
        
        return Command::SUCCESS;
    }
}
