<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbsentAttendance extends Command
{
    protected $signature = 'attendance:mark-absent
                            {--date= : Completed date to process (YYYY-MM-DD)}
                            {--from= : First completed date to process (YYYY-MM-DD)}
                            {--to= : Last completed date to process (YYYY-MM-DD)}';

    protected $description = 'Create absent attendance records for employees with no time in on completed workdays';

    public function handle(): int
    {
        [$from, $to] = $this->resolveDateRange();

        if ($from->greaterThan($to)) {
            $this->error('The start date must be on or before the end date.');
            return Command::FAILURE;
        }

        $employees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->get(['id', 'date_of_hire']);
        $marked = 0;

        for ($date = $from->copy(); $date->lessThanOrEqualTo($to); $date->addDay()) {
            if ($date->isWeekend() || Holiday::whereDate('date', $date)->exists()) {
                continue;
            }

            $leaveUserIds = Leave::whereIn('status', ['approved', 'paid'])
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->pluck('user_id');

            foreach ($employees as $employee) {
                if ($employee->date_of_hire && $date->lessThan($employee->date_of_hire)) {
                    continue;
                }

                if ($leaveUserIds->contains($employee->id)) {
                    continue;
                }

                $attendance = Attendance::firstOrNew([
                    'user_id' => $employee->id,
                    'date' => $date->toDateString(),
                ]);

                if ($attendance->exists && $attendance->time_in) {
                    continue;
                }

                if ($attendance->status === 'on leave') {
                    continue;
                }

                $attendance->time_in = null;
                $attendance->time_out = null;
                $attendance->status = 'absent';
                $attendance->is_manual = false;
                $attendance->save();
                $marked++;
            }
        }

        $this->info("Marked {$marked} attendance record(s) as absent from {$from->toDateString()} to {$to->toDateString()}.");
        return Command::SUCCESS;
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function resolveDateRange(): array
    {
        if ($this->option('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay();
            return [$date, $date->copy()];
        }

        $from = $this->option('from')
            ? Carbon::createFromFormat('Y-m-d', $this->option('from'))->startOfDay()
            : today()->subDay()->startOfDay();
        $to = $this->option('to')
            ? Carbon::createFromFormat('Y-m-d', $this->option('to'))->startOfDay()
            : $from->copy();

        return [$from, $to];
    }
}
