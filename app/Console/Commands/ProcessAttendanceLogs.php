<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Console\Command;

class ProcessAttendanceLogs extends Command
{
    protected $signature = 'attendance:process-logs {--user-id= : Process logs for specific user ID} {--date= : Process logs for specific date (YYYY-MM-DD)}';

    protected $description = 'Process attendance logs and create/update attendance records from scanned time in/out data';

    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    public function handle()
    {
        $userId = $this->option('user-id');
        $date = $this->option('date');

        if ($userId) {
            // Process for specific user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }

            $this->attendanceService->processAttendanceLogs($userId);
            $this->info("Attendance logs processed for user: {$user->name}");
            return 0;
        }

        // Process all users
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $this->attendanceService->processAttendanceLogs($user->id);
            $count++;
        }

        $this->info("Attendance logs processed for {$count} user(s).");
        return 0;
    }
}
