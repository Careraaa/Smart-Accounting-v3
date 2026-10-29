<?php

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function markAbsentTestEmployee(Shift $shift, int $workDaysPerWeek): User
{
    static $sequence = 0;
    $sequence++;

    return User::create([
        'name' => "Attendance Test {$sequence}",
        'username' => "attendance-test-{$sequence}",
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'shift_id' => $shift->id,
        'work_days_per_week' => $workDaysPerWeek,
    ]);
}

it('marks a six-day employee absent on Saturday but not a five-day employee', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-26 18:00:00'));

    $shift = Shift::create([
        'name' => 'Attendance Test Day Shift',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'is_active' => true,
    ]);
    $sixDayEmployee = markAbsentTestEmployee($shift, 6);
    $fiveDayEmployee = markAbsentTestEmployee($shift, 5);

    $this->artisan('attendance:mark-absent', ['--date' => '2026-10-24'])
        ->assertSuccessful();

    expect(Attendance::where('user_id', $sixDayEmployee->id)
        ->whereDate('date', '2026-10-24')
        ->where('status', 'absent')
        ->exists())->toBeTrue();
    expect(Attendance::where('user_id', $fiveDayEmployee->id)
        ->whereDate('date', '2026-10-24')
        ->exists())->toBeFalse();
});

it('waits until an overnight shift ends before marking absence', function () {
    $shift = Shift::create([
        'name' => 'Attendance Test Overnight Shift',
        'start_time' => '21:00:00',
        'end_time' => '06:00:00',
        'is_active' => true,
    ]);
    $employee = markAbsentTestEmployee($shift, 5);

    Carbon::setTestNow(Carbon::parse('2026-10-27 05:59:00'));
    $this->artisan('attendance:mark-absent', ['--date' => '2026-10-26'])
        ->assertSuccessful();

    expect(Attendance::where('user_id', $employee->id)->whereDate('date', '2026-10-26')->exists())
        ->toBeFalse();

    Carbon::setTestNow(Carbon::parse('2026-10-27 06:01:00'));
    $this->artisan('attendance:mark-absent', ['--date' => '2026-10-26'])
        ->assertSuccessful();

    expect(Attendance::where('user_id', $employee->id)
        ->whereDate('date', '2026-10-26')
        ->where('status', 'absent')
        ->exists())->toBeTrue();
});
