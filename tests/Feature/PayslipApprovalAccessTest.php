<?php

use App\Models\Payroll;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\PayrollBatch;
use App\Models\PayrollAllowance;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\ThirteenthMonthPayController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('lists only approved batches on the payslips page', function () {
    $employee = User::create([
        'name' => 'Approved Payslip Employee',
        'username' => 'approved-payslip-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    foreach (['approved', 'draft', 'submitted', 'rejected'] as $status) {
        $batch = PayrollBatch::create([
            'period_start' => '2026-10-01',
            'period_end' => '2026-10-15',
            'status' => $status,
        ]);

        Payroll::create([
            'batch_id' => $batch->id,
            'user_id' => $employee->id,
            'payroll_period_start' => $batch->period_start,
            'payroll_period_end' => $batch->period_end,
            'status' => $status,
        ]);
    }

    $view = app(PayrollController::class)->generatePayslipIndex(new Request());

    expect($view->getData()['allBatches']->pluck('status')->all())->toBe(['approved']);
});

it('blocks payslip access for payrolls in an unapproved batch', function () {
    $employee = User::create([
        'name' => 'Payslip Test Employee',
        'username' => 'payslip-test-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $batch = PayrollBatch::create([
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-15',
        'status' => 'draft',
    ]);

    $payroll = Payroll::create([
        'batch_id' => $batch->id,
        'user_id' => $employee->id,
        'payroll_period_start' => $batch->period_start,
        'payroll_period_end' => $batch->period_end,
        'status' => 'prepared',
    ]);

    expect(fn () => app(PayrollController::class)->batchPayslips($batch))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    expect(fn () => app(PayrollController::class)->batchPrintPayslips($batch))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    expect(fn () => app(PayrollController::class)->generatePayslip($payroll))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('shows persisted paid leave on the payslip without duplicated zero-day rows', function () {
    $employee = User::create([
        'name' => 'Paid Leave Payslip Employee',
        'username' => 'paid-leave-payslip-employee',
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
        'status' => 'paid',
        'paid_days' => 3,
    ]);

    $batch = PayrollBatch::create([
        'period_start' => '2026-10-16',
        'period_end' => '2026-10-31',
        'status' => 'approved',
    ]);

    $payroll = Payroll::create([
        'batch_id' => $batch->id,
        'user_id' => $employee->id,
        'payroll_period_start' => $batch->period_start,
        'payroll_period_end' => $batch->period_end,
        'status' => 'approved',
    ]);

    $payroll->allowances()->create([
        'allowance_type' => 'Leave Pay (Sick Leave)',
        'hours' => 3,
        'amount' => 3000,
    ]);
    $payroll->allowances()->create([
        'allowance_type' => 'Leave Pay (Sick Leave)',
        'hours' => 0,
        'amount' => 3000,
    ]);

    $view = app(PayrollController::class)->generatePayslip($payroll);
    $renderedPayroll = $view->getData()['payroll'];

    expect($renderedPayroll->leave_breakdown)->toBe([
        ['type_name' => 'Sick Leave', 'amount' => 3000.0, 'paid_days' => 3],
    ])->and((float) $renderedPayroll->gross_pay)->toBe(3000.0);
});

it('blocks payslip access for an unapproved thirteenth-month batch', function () {
    $batch = PayrollBatch::create([
        'type' => 'thirteenth_month',
        'period_start' => '2026-01-01',
        'period_end' => '2026-12-31',
        'status' => 'draft',
    ]);

    expect(fn () => app(ThirteenthMonthPayController::class)->batchPayslips($batch))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});
