<?php

use App\Models\Payroll;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\PayrollBatch;
use App\Models\PayrollAllowance;
use App\Models\ThirteenthMonthPay;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\ThirteenthMonthPayController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

function authenticatePayslipTestUser(User $user): void
{
    Auth::viaRequest('payslip-test', static fn () => $user);
    config(['auth.guards.payslip-test' => ['driver' => 'payslip-test']]);
    config(['auth.defaults.guard' => 'payslip-test']);
    Auth::forgetGuards();
}

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

it('lists only the signed-in user payslips from approved payrolls', function () {
    $employee = User::create([
        'name' => 'Payslip Owner',
        'username' => 'payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $otherEmployee = User::create([
        'name' => 'Another Employee',
        'username' => 'another-payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $approvedBatch = PayrollBatch::create([
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-15',
        'status' => 'approved',
    ]);
    $unapprovedBatch = PayrollBatch::create([
        'period_start' => '2026-11-01',
        'period_end' => '2026-11-15',
        'status' => 'submitted',
    ]);

    foreach ([[$employee, $approvedBatch], [$otherEmployee, $approvedBatch], [$employee, $unapprovedBatch]] as [$owner, $batch]) {
        Payroll::create([
            'batch_id' => $batch->id,
            'user_id' => $owner->id,
            'payroll_period_start' => $batch->period_start,
            'payroll_period_end' => $batch->period_end,
            'status' => $batch->status === 'approved' ? 'approved' : 'submitted',
        ]);
    }

    authenticatePayslipTestUser($employee);
    $view = app(PayrollController::class)->myPayslips();

    expect($view->getData()['payrolls']->pluck('user_id')->all())->toBe([$employee->id])
        ->and($view->getData()['payrolls']->pluck('payroll_period_start')->map->format('Y-m-d')->all())->toBe(['2026-10-01']);
});

it('allows viewing only an approved payslip belonging to the signed-in user', function () {
    $employee = User::create([
        'name' => 'Payslip Owner',
        'username' => 'approved-payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $otherEmployee = User::create([
        'name' => 'Another Employee',
        'username' => 'other-approved-payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $batch = PayrollBatch::create([
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-15',
        'status' => 'approved',
    ]);
    $payroll = Payroll::create([
        'batch_id' => $batch->id,
        'user_id' => $employee->id,
        'payroll_period_start' => $batch->period_start,
        'payroll_period_end' => $batch->period_end,
        'status' => 'approved',
    ]);

    authenticatePayslipTestUser($employee);
    expect(app(PayrollController::class)->myPayslip($payroll)->name())
        ->toBe('hr.payroll.generate-payslip.payslip');

    authenticatePayslipTestUser($otherEmployee);
    expect(fn () => app(PayrollController::class)->myPayslip($payroll))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('excludes superadmins and qr admins from self-service payslips', function () {
    $batch = PayrollBatch::create([
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-15',
        'status' => 'approved',
    ]);

    foreach (['superadmin', 'qr_admin'] as $role) {
        $user = User::create([
            'name' => "{$role} Payslip User",
            'username' => "{$role}-payslip-user",
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);
        Payroll::create([
            'batch_id' => $batch->id,
            'user_id' => $user->id,
            'payroll_period_start' => $batch->period_start,
            'payroll_period_end' => $batch->period_end,
            'status' => 'approved',
        ]);

        authenticatePayslipTestUser($user);
        expect(fn () => app(PayrollController::class)->myPayslips())
            ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    }
});

it('allows every non-admin payroll role to access self-service payslips', function () {
    foreach (['employee', 'hr', 'accountant', 'remittance_clerk'] as $role) {
        $user = User::create([
            'name' => "{$role} Payslip User",
            'username' => "{$role}-self-service-payslip-user",
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);

        authenticatePayslipTestUser($user);
        expect(app(PayrollController::class)->myPayslips()->name())
            ->toBe('employee.payslips.index');
    }
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

it('allows viewing only an approved thirteenth-month payslip belonging to the signed-in user', function () {
    $employee = User::create([
        'name' => '13th Month Payslip Owner',
        'username' => 'thirteenth-month-payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $otherEmployee = User::create([
        'name' => 'Another 13th Month Employee',
        'username' => 'another-thirteenth-month-payslip-owner',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $batch = PayrollBatch::create([
        'type' => 'thirteenth_month',
        'period_start' => '2026-01-01',
        'period_end' => '2026-12-31',
        'status' => 'approved',
    ]);
    $record = ThirteenthMonthPay::create([
        'batch_id' => $batch->id,
        'user_id' => $employee->id,
        'calendar_year' => 2026,
        'thirteenth_month_pay' => 1000,
        'computation_breakdown' => [],
    ]);

    authenticatePayslipTestUser($employee);
    expect(app(ThirteenthMonthPayController::class)->myPayslip($batch, $record)->name())
        ->toBe('hr.payroll.thirteenth-month.payslip');

    authenticatePayslipTestUser($otherEmployee);
    expect(fn () => app(ThirteenthMonthPayController::class)->myPayslip($batch, $record))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});
