<?php

use App\Models\Payroll;
use App\Models\PayrollBatch;
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
