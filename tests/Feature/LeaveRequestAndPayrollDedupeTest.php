<?php

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\User;
use App\Services\LeaveService;
use App\Services\PayrollService;
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

it('does not count generated leave pay as a manual allowance when recomputing payroll', function () {
    $employee = User::create([
        'name' => 'Payroll Leave Recompute Employee',
        'username' => 'payroll-leave-recompute',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'salary_rate' => 1000,
    ]);

    $leaveType = LeaveType::create([
        'name' => 'Sick Leave',
        'days_allowed' => 10,
        'status' => 'active',
    ]);

    Leave::create([
        'user_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'leave_type' => $leaveType->name,
        'start_date' => '2026-10-29',
        'end_date' => '2026-10-31',
        'reason' => 'Approved leave',
        'status' => 'approved',
        'paid_days' => 3,
    ]);

    $payroll = Payroll::create([
        'user_id' => $employee->id,
        'payroll_period_start' => '2026-10-16',
        'payroll_period_end' => '2026-10-31',
        'status' => 'prepared',
    ]);

    $payroll = app(PayrollService::class)->updatePayroll(
        $payroll,
        $employee,
        Carbon::parse('2026-10-16'),
        Carbon::parse('2026-10-31'),
        [['name' => 'Leave Pay (Sick Leave)', 'amount' => 3000]],
    );

    $leaveAllowances = $payroll->allowances
        ->filter(fn ($allowance) => str_starts_with($allowance->allowance_type, 'Leave Pay'));

    expect($leaveAllowances)->toHaveCount(1)
        ->and($leaveAllowances->first()->amount)->toBe(3000.0)
        ->and((int) $leaveAllowances->first()->hours)->toBe(3)
        ->and((float) $payroll->gross_pay)->toBe(3000.0);
});
