<?php

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('detects overlapping active leave dates but ignores rejected requests', function (string $status, bool $isActive) {
    $employee = User::create([
        'name' => 'Leave Test Employee',
        'username' => 'leave-test-' . $status,
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $leaveType = LeaveType::create([
        'name' => 'Sick Leave',
        'days_allowed' => 10,
        'status' => 'active',
    ]);

    $date = now()->addDays(3)->toDateString();
    $existingLeave = Leave::create([
        'user_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'leave_type' => $leaveType->name,
        'start_date' => $date,
        'end_date' => $date,
        'reason' => 'Existing request',
        'status' => $status,
    ]);

    expect(Leave::overlappingActiveDates($employee->id, $date, $date)->exists())->toBe($isActive)
        ->and(Leave::overlappingActiveDates($employee->id, $date, $date, $existingLeave->id)->exists())->toBeFalse()
        ->and(Leave::overlappingActiveDates($employee->id, now()->addDays(4)->toDateString(), now()->addDays(4)->toDateString())->exists())->toBeFalse();
})->with([
    ['pending', true],
    ['approved', true],
    ['paid', true],
    ['rejected', false],
]);

it('pays overlapping approved leave dates only once', function () {
    $employee = User::create([
        'name' => 'Payroll Leave Employee',
        'username' => 'payroll-leave-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $leaveType = LeaveType::create([
        'name' => 'Sick Leave',
        'days_allowed' => 10,
        'status' => 'active',
    ]);

    foreach ([
        ['2026-10-29', '2026-10-29', 1],
        ['2026-10-29', '2026-10-29', 1],
        ['2026-10-30', '2026-10-31', 2],
    ] as [$start, $end, $paidDays]) {
        Leave::create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'leave_type' => $leaveType->name,
            'start_date' => $start,
            'end_date' => $end,
            'reason' => 'Approved leave',
            'status' => 'approved',
            'paid_days' => $paidDays,
        ]);
    }

    $allowances = app(LeaveService::class)->getApprovedLeavesAllowances(
        $employee->id,
        Carbon::parse('2026-10-16'),
        Carbon::parse('2026-10-31'),
        1000
    );

    expect($allowances)->toHaveCount(1)
        ->and($allowances[0]['paid_days'])->toBe(3)
        ->and($allowances[0]['amount'])->toBe(3000.0);
});
