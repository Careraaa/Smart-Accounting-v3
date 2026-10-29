<?php

use App\Models\CashAdvance;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\CashAdvanceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('notifies HR when an employee submits a cash advance request', function () {
    $employee = User::create([
        'name' => 'Cash Advance Employee',
        'username' => 'cash-advance-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);
    $hr = User::create([
        'name' => 'Cash Advance HR',
        'username' => 'cash-advance-hr',
        'password' => 'password',
        'role' => 'hr',
        'status' => 'active',
    ]);
    $accountant = User::create([
        'name' => 'Cash Advance Accountant',
        'username' => 'cash-advance-accountant',
        'password' => 'password',
        'role' => 'accountant',
        'status' => 'active',
    ]);

    $cashAdvance = CashAdvance::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'repayment_months' => 5,
        'monthly_deduction' => 1000,
        'request_date' => today(),
        'status' => 'pending',
    ]);

    CashAdvanceNotification::submitted($cashAdvance);

    expect(Notification::where('user_id', $hr->id)
        ->where('type', 'cash_advance_pending')
        ->whereJsonContains('data->cash_advance_id', $cashAdvance->id)
        ->exists())->toBeTrue();
    expect(Notification::where('user_id', $accountant->id)
        ->where('type', 'cash_advance_pending')
        ->exists())->toBeTrue();
    expect(Notification::where('user_id', $employee->id)
        ->where('type', 'cash_advance_submitted')
        ->exists())->toBeTrue();
});
