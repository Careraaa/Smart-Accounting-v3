<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Shift;
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

        $employees = Employee::with('shift')
            ->whereNotIn('role', ['superadmin', 'qr_admin'])
            ->get(['id', 'date_of_hire', 'work_days_per_week', 'shift_id']);
        $fallbackShift = $employees->contains(fn (Employee $employee) => !$employee->shift)
            ? (Shift::where('is_active', true)->first() ?? Shift::orderBy('created_at')->first())
            : null;
        $marked = 0;
        $missingShift = 0;
        $now = now();

        for ($date = $from->copy(); $date->lessThanOrEqualTo($to); $date->addDay()) {
            if ($date->dayOfWeekIso > 6 || Holiday::whereDate('date', $date)->exists()) {
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

                $workDaysPerWeek = (int) ($employee->work_days_per_week ?? 5);
                if ($date->dayOfWeekIso > ($workDaysPerWeek === 6 ? 6 : 5)) {
                    continue;
                }

                if ($leaveUserIds->contains($employee->id)) {
                    continue;
                }

                $shift = $employee->shift ?? $fallbackShift;
                if (!$shift) {
                    $missingShift++;
                    continue;
                }

                $shiftStart = Carbon::parse($date->toDateString() . ' ' . $shift->start_time);
                $shiftEnd = Carbon::parse($date->toDateString() . ' ' . $shift->end_time);
                if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
                    $shiftEnd->addDay();
                }

                if ($shiftEnd->greaterThan($now)) {
                    continue;
                }

                $attendance = Attendance::firstOrNew([
                    'user_id' => $employee->id,
                    'date' => $date->toDateString(),
                ]);

                if ($attendance->exists && ($attendance->time_in || $attendance->status === 'absent')) {
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
        if ($missingShift > 0) {
            $this->warn("Skipped {$missingShift} employee-date check(s) because no shift is assigned or configured.");
        }

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
            : ($this->option('from') ? $from->copy() : today()->startOfDay());

        return [$from, $to];
    }
}
