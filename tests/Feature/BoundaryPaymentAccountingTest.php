<?php

use App\Models\AccountingAuditLog;
use App\Models\AccountMapping;
use App\Models\ChartOfAccount;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\JournalEntry;
use App\Models\PAO;
use App\Models\Route as TransportRoute;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->accountant = User::create([
        'name' => 'Boundary Accountant',
        'username' => 'boundary-accountant',
        'password' => 'password',
        'role' => 'accountant',
        'status' => 'active',
    ]);
    $this->clerk = User::create([
        'name' => 'Boundary Clerk',
        'username' => 'boundary-clerk',
        'password' => 'password',
        'role' => 'remittance_clerk',
        'status' => 'active',
    ]);
    $this->actingAs($this->accountant);

    $this->cash = ChartOfAccount::create([
        'account_code' => '1010',
        'account_name' => 'Cash',
        'account_type' => 'Asset',
        'is_active' => true,
    ]);
    $this->revenue = ChartOfAccount::create([
        'account_code' => '4010',
        'account_name' => 'Boundary Revenue',
        'account_type' => 'Revenue',
        'is_active' => true,
    ]);
    AccountMapping::create([
        'mapping_key' => 'boundary_cash',
        'label' => 'Boundary Payment Cash / Bank',
        'account_id' => $this->cash->id,
    ]);
    AccountMapping::create([
        'mapping_key' => 'boundary_revenue',
        'label' => 'Boundary Revenue',
        'account_id' => $this->revenue->id,
    ]);
});

function boundaryPaymentTestRemittance(float $boundary, float $netRemittance, string $status = 'pending'): DailyRemittance
{
    $driver = Driver::create([
        'name' => 'Dina Driver',
        'license_number' => 'DL-'.Str::uuid(),
        'contact_number' => '09170000001',
        'email' => 'driver-'.Str::uuid().'@example.test',
        'status' => 'active',
    ]);
    $pao = PAO::create([
        'name' => 'Pablo PAO',
        'contact_number' => '09170000002',
        'email' => 'pao-'.Str::uuid().'@example.test',
        'date_of_hire' => now()->toDateString(),
        'status' => 'active',
    ]);
    $route = TransportRoute::create([
        'route_name' => 'North Route',
        'origin' => 'North',
        'destination' => 'South',
        'weekday_boundary' => $boundary,
        'weekend_boundary' => $boundary,
        'status' => 'active',
    ]);
    $vehicle = Vehicle::create([
        'plate_number' => 'ABC-'.random_int(1000, 9999),
        'route_id' => $route->id,
        'operator' => 'Knights Transport',
        'status' => 'active',
    ]);

    return DailyRemittance::create([
        'driver_id' => $driver->id,
        'pao_id' => $pao->id,
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'remittance_date' => now()->toDateString(),
        'total_collection' => 5000,
        'diesel' => 150,
        'total_expenses' => 150,
        'net_remittance' => $netRemittance,
        'boundary' => $boundary,
        'is_short_remittance' => $netRemittance < $boundary,
        'short_amount' => max($boundary - $netRemittance, 0),
        'driver_share' => max($boundary - $netRemittance, 0) / 2,
        'pao_share' => max($boundary - $netRemittance, 0) / 2,
        'driver_amount_paid' => 0,
        'driver_status' => 'pending',
        'pao_amount_paid' => 0,
        'pao_status' => 'pending',
        'status' => $status,
    ]);
}

function boundaryPaymentPayload(string $token, string $date, string $amount, string $paidBy = 'driver'): array
{
    return [
        'source_payment_key' => $token,
        'payment_date' => $date,
        'amount' => $amount,
        'paid_by' => $paidBy,
        'confirm_boundary_received' => '1',
    ];
}

test('payroll receivables shows the short remittances tab only to accountants', function () {
    $hr = User::create([
        'name' => 'Boundary HR',
        'username' => 'boundary-hr',
        'password' => 'password',
        'role' => 'hr',
        'status' => 'active',
    ]);
    $superadmin = User::create([
        'name' => 'Boundary Superadmin',
        'username' => 'boundary-superadmin',
        'password' => 'password',
        'role' => 'superadmin',
        'status' => 'active',
    ]);
    $remittance = boundaryPaymentTestRemittance(3000, 2000, 'approved');
    $paymentKey = (string) Str::uuid();
    app(\App\Services\BoundaryPaymentService::class)->recordPayment(
        $remittance->id,
        now()->toDateString(),
        100,
        'driver',
        $paymentKey,
        $this->clerk->id
    );

    $this->actingAs($hr)
        ->get(route('payroll.receivables.index', ['tab' => 'short_remittances']))
        ->assertOk()
        ->assertSee('Cash Advances')
        ->assertDontSee('Short Remittances')
        ->assertDontSee('Dina Driver')
        ->assertDontSee('Pending payment actions');

    $pendingPayment = $remittance->boundaryPayments()->where('source_payment_key', $paymentKey)->firstOrFail();
    $this->actingAs($hr)
        ->get(route('payroll.receivables.index', ['tab' => 'short_remittances']))
        ->assertDontSee(route('remittance-approval.boundary-payments.approve', $pendingPayment), false)
        ->assertDontSee(route('remittance-approval.boundary-payments.reject', $pendingPayment), false);

    $this->actingAs($superadmin)
        ->withSession(['view_as_role' => 'hr'])
        ->get(route('payroll.receivables.index', ['tab' => 'short_remittances']))
        ->assertOk()
        ->assertSee('Cash Advances')
        ->assertDontSee('Short Remittances')
        ->assertDontSee('Dina Driver');

    $this->actingAs($superadmin)
        ->withSession(['view_as_role' => 'accountant'])
        ->get(route('payroll.receivables.index'))
        ->assertOk()
        ->assertSee('Short Remittances');

    $this->actingAs($this->accountant)
        ->get(route('payroll.receivables.index', ['tab' => 'short_remittances']))
        ->assertOk()
        ->assertSee('Pending payment actions')
        ->assertSee('BP-'.str_pad($pendingPayment->id, 6, '0', STR_PAD_LEFT))
        ->assertSee('Approve')
        ->assertSee('Reject')
        ->assertSee('Recorded by Boundary Clerk')
        ->assertSee('Reviewed by Pending')
        ->assertSee(route('remittance-approval.boundary-payments.approve', $pendingPayment), false)
        ->assertSee(route('remittance-approval.boundary-payments.reject', $pendingPayment), false);
});

test('food allowance and other expenses reduce boundary when creating a remittance', function () {
    $remittance = boundaryPaymentTestRemittance(3000, 2000);

    $this->actingAs($this->clerk)->post(route('remittances.store'), [
        'driver_id' => $remittance->driver_id,
        'pao_id' => $remittance->pao_id,
        'vehicle_id' => $remittance->vehicle_id,
        'remittance_date' => now()->toDateString(),
        'total_collection' => 5000,
        'food_allowance' => 100,
        'others' => 200,
        'net_remittance' => 2600,
        'vehicle_status' => 'active',
    ])->assertRedirect(route('remittances.index'));

    $createdRemittance = DailyRemittance::latest('id')->firstOrFail();
    expect((float) $createdRemittance->boundary)->toBe(2700.0)
        ->and((float) $createdRemittance->total_expenses)->toBe(300.0)
        ->and((float) $createdRemittance->short_amount)->toBe(100.0)
        ->and((float) $createdRemittance->driver_share)->toBe(50.0)
        ->and((float) $createdRemittance->pao_share)->toBe(50.0);
});

test('partial and later boundary payments post once and flow into accounting reports', function () {
    expect(Schema::hasColumn('boundary_payments', 'paid_by'))->toBeTrue();

    $remittance = boundaryPaymentTestRemittance(3000, 2000);
    $today = now()->toDateString();
    $this->get(route('remittance-approval.show', $remittance))
        ->assertOk()
        ->assertSee('Boundary amount to confirm')
        ->assertSee('readonly', false)
        ->assertSee('value="2000.00"', false)
        ->assertDontSee('name="boundary_payment_amount"', false)
        ->assertDontSee('Paid by')
        ->assertSee('No receipt is required.');
    $this->actingAs($this->clerk)
        ->get(route('short-remittances.edit', $remittance))
        ->assertOk()
        ->assertSee('must be approved before a shortage payment can be recorded');
    $this->actingAs($this->accountant)
        ->get(route('remittance-approval.show', $remittance))
        ->assertDontSee('name="amount"', false)
        ->assertDontSee('Record payment');

    $this->post(route('remittance-approval.approve', $remittance), [
        'boundary_payment_amount' => '9999.00',
        'boundary_payment_date' => $today,
        'confirm_boundary_received' => '1',
    ])->assertRedirect();

    expect($remittance->fresh()->status)->toBe('approved')
        ->and((float) $remittance->fresh()->short_amount)->toBe(1000.0)
        ->and($remittance->boundaryPayments()->count())->toBe(1);
    $this->actingAs($this->clerk)
        ->get(route('short-remittances.edit', $remittance))
        ->assertOk()
        ->assertSee('Record received payment')
        ->assertSee('Submit for approval');
    $this->actingAs($this->accountant);

    $initialPayment = $remittance->boundaryPayments()->firstOrFail();
    expect($initialPayment->payment_kind)->toBe('initial')
        ->and((float) $initialPayment->amount)->toBe(2000.0)
        ->and($initialPayment->paid_by)->toBeNull()
        ->and($initialPayment->recorded_by)->toBe($this->accountant->id)
        ->and($initialPayment->approved_by)->toBe($this->accountant->id)
        ->and($initialPayment->journalEntry->status)->toBe('Posted')
        ->and((int) $initialPayment->journalEntry->reference_id)->toBe($initialPayment->id)
        ->and((int) $initialPayment->journalEntry->boundaryPayment->id)->toBe($initialPayment->id)
        ->and($initialPayment->journalEntry->description)->toContain('Dina Driver')
        ->and($initialPayment->journalEntry->description)->toContain('Pablo PAO')
        ->and($initialPayment->journalEntry->description)->toContain('North Route');

    $this->post(route('remittance-approval.approve', $remittance), [
        'boundary_payment_date' => $today,
        'confirm_boundary_received' => '1',
    ])->assertRedirect();
    expect($remittance->boundaryPayments()->count())->toBe(1);

    $firstInstallmentKey = (string) Str::uuid();
    $this->actingAs($this->clerk);
    $this->post(
        route('short-remittances.payments.store', $remittance),
        boundaryPaymentPayload($firstInstallmentKey, $today, '501.00', 'driver')
    )->assertSessionHas('error');
    expect($remittance->boundaryPayments()->count())->toBe(1)
        ->and((float) $remittance->fresh()->driver_amount_paid)->toBe(0.0)
        ->and((float) $remittance->fresh()->pao_amount_paid)->toBe(0.0);

    $this->post(
        route('short-remittances.payments.store', $remittance),
        boundaryPaymentPayload($firstInstallmentKey, $today, '500.00', 'driver')
    )->assertRedirect();
    $pendingPayment = $remittance->boundaryPayments()->where('source_payment_key', $firstInstallmentKey)->firstOrFail();
    expect($pendingPayment->status)->toBe('Pending')
        ->and($pendingPayment->approved_by)->toBeNull()
        ->and($pendingPayment->journal_entry_id)->toBeNull()
        ->and((float) $remittance->fresh()->short_amount)->toBe(1000.0)
        ->and((float) $remittance->fresh()->driver_amount_paid)->toBe(0.0);
    $this->actingAs($this->accountant)
        ->get(route('remittance-approval.show', $remittance))
        ->assertDontSee(route('remittance-approval.boundary-payments.approve', $pendingPayment), false)
        ->assertDontSee(route('remittance-approval.boundary-payments.reject', $pendingPayment), false)
        ->assertDontSee('>Action<', false);
    $this->actingAs($this->clerk);
    $this->post(
        route('short-remittances.payments.store', $remittance),
        boundaryPaymentPayload($firstInstallmentKey, $today, '500.00', 'driver')
    )->assertRedirect();
    expect($remittance->boundaryPayments()->count())->toBe(2);

    $this->actingAs($this->accountant)
        ->post(route('remittance-approval.boundary-payments.approve', $pendingPayment))
        ->assertRedirect();
    expect((float) $remittance->fresh()->short_amount)->toBe(500.0)
        ->and((float) $remittance->fresh()->driver_amount_paid)->toBe(500.0)
        ->and((float) $remittance->fresh()->pao_amount_paid)->toBe(0.0)
        ->and($remittance->fresh()->driver_status)->toBe('paid')
        ->and($remittance->fresh()->pao_status)->toBe('pending');

    $paoPaymentKey = (string) Str::uuid();
    $this->actingAs($this->clerk)->post(
        route('short-remittances.payments.store', $remittance),
        boundaryPaymentPayload($paoPaymentKey, $today, '500.00', 'pao')
    )->assertRedirect();
    $paoPayment = $remittance->boundaryPayments()->where('source_payment_key', $paoPaymentKey)->firstOrFail();
    $this->actingAs($this->accountant)
        ->post(route('remittance-approval.boundary-payments.approve', $paoPayment))
        ->assertRedirect();

    $remittance->refresh();
    expect($remittance->boundaryPayments()->count())->toBe(3)
        ->and((float) $remittance->boundaryPayments()->sum('amount'))->toBe(3000.0)
        ->and((float) $remittance->short_amount)->toBe(0.0)
        ->and((float) $remittance->driver_amount_paid)->toBe(500.0)
        ->and((float) $remittance->pao_amount_paid)->toBe(500.0)
        ->and($remittance->driver_status)->toBe('paid')
        ->and($remittance->pao_status)->toBe('paid')
        ->and(JournalEntry::where('reference_type', 'Boundary Payment')->where('status', 'Posted')->count())->toBe(3)
        ->and(AccountingAuditLog::where('module', 'Boundary Payment')->where('action', 'posted')->count())->toBe(3);
    $this->get(route('remittance-approval.show', $remittance))
        ->assertOk()
        ->assertSee('Boundary Payments')
        ->assertSee('Driver share')
        ->assertSee('PAO share')
        ->assertSee('BP-000001');
    $this->actingAs($this->clerk)
        ->get(route('short-remittances.edit', $remittance))
        ->assertOk()
        ->assertSee('Driver shortage share')
        ->assertSee('PAO shortage share')
        ->assertSee('Share remaining');

    $this->actingAs($this->accountant);
    $totals = app(\App\Services\AccountingReportService::class)->postedTotals();
    expect((float) $totals->get($this->cash->id)->debit_total)->toBe(3000.0)
        ->and((float) $totals->get($this->revenue->id)->credit_total)->toBe(3000.0);

    $this->get(route('accounting.income-statement.index', [
        'date_from' => $today,
        'date_to' => $today,
    ]))->assertOk()->assertSee('Boundary Revenue')->assertSee('3,000.00');
    $this->get(route('accounting.general-ledger.index', [
        'account_id' => $this->cash->id,
        'date_from' => $today,
        'date_to' => $today,
    ]))->assertOk()->assertSee('3,000.00');
    $this->get(route('accounting.trial-balance.index', ['as_of' => $today]))
        ->assertOk()->assertSee('Cash')->assertSee('3,000.00')->assertSee('BALANCED');
    $this->get(route('accounting.balance-sheet.index', ['as_of' => $today]))
        ->assertOk()->assertSee('BALANCE SHEET')->assertSee('3,000.00')->assertSee('BALANCED');

    $this->actingAs($this->accountant)
        ->post(
            route('short-remittances.payments.store', $remittance),
            boundaryPaymentPayload((string) Str::uuid(), $today, '1.00')
        )->assertForbidden();

    $this->actingAs($this->clerk)
        ->post(route('remittance-approval.boundary-payments.approve', $pendingPayment))
        ->assertForbidden();

    $this->put(route('remittances.update', $remittance), [
        'driver_id' => $remittance->driver_id,
        'pao_id' => $remittance->pao_id,
        'vehicle_id' => $remittance->vehicle_id,
        'remittance_date' => $today,
        'total_collection' => 5000,
        'net_remittance' => 2000,
        'vehicle_status' => 'active',
    ])->assertSessionHas('error');
    expect((float) $remittance->fresh()->boundary)->toBe(3000.0);
});

test('full payment posts only the boundary and unpaid remittances create no entry', function () {
    $full = boundaryPaymentTestRemittance(3000, 4000);
    $today = now()->toDateString();

    $this->post(route('remittance-approval.approve', $full), [
        'boundary_payment_date' => $today,
    ])->assertSessionHasErrors('confirm_boundary_received');
    expect($full->fresh()->status)->toBe('pending')
        ->and($full->boundaryPayments()->count())->toBe(0);

    $this->post(route('remittance-approval.approve', $full), [
        'boundary_payment_date' => $today,
        'confirm_boundary_received' => '1',
    ])->assertRedirect();

    expect($full->boundaryPayments()->firstOrFail()->paid_by)->toBeNull()
        ->and((float) $full->boundaryPayments()->sum('amount'))->toBe(3000.0)
        ->and((float) $full->fresh()->short_amount)->toBe(0.0)
        ->and((float) $full->boundaryPayments()->first()->journalEntry->lines()->where('account_id', $this->revenue->id)->sum('credit'))->toBe(3000.0);

    $unpaid = boundaryPaymentTestRemittance(3000, 0);
    $this->post(route('remittance-approval.approve', $unpaid), [])->assertRedirect();

    expect($unpaid->boundaryPayments()->count())->toBe(0)
        ->and((float) $unpaid->fresh()->short_amount)->toBe(3000.0)
        ->and(JournalEntry::where('reference_type', 'Boundary Payment')->count())->toBe(1);

    $legacyShortage = boundaryPaymentTestRemittance(3000, 2000);
    $this->actingAs($this->clerk)
        ->get(route('short-remittances.edit', $legacyShortage))
        ->assertOk()
        ->assertSee('Driver shortage share')
        ->assertSee('PAO shortage share')
        ->assertSee('Each submission stays pending until an accountant approves it.');
    $this->actingAs($this->clerk)
        ->put(route('short-remittances.update', $legacyShortage), [
            'driver_amount_paid' => 1000,
            'driver_status' => 'paid',
            'pao_amount_paid' => 0,
            'pao_status' => 'paid',
        ])->assertSessionHas('error');
    expect($legacyShortage->fresh()->driver_status)->toBe('pending')
        ->and((float) $legacyShortage->fresh()->driver_amount_paid)->toBe(0.0);
});

test('accountant can reject a clerk payment without posting or changing balances', function () {
    $remittance = boundaryPaymentTestRemittance(3000, 0, 'approved');
    $paymentKey = (string) Str::uuid();

    $this->actingAs($this->clerk)->post(
        route('short-remittances.payments.store', $remittance),
        boundaryPaymentPayload($paymentKey, now()->toDateString(), '200.00', 'driver')
    )->assertRedirect();
    $payment = $remittance->boundaryPayments()->where('source_payment_key', $paymentKey)->firstOrFail();

    $this->actingAs($this->accountant)
        ->post(route('remittance-approval.boundary-payments.reject', $payment))
        ->assertRedirect();

    expect($payment->fresh()->status)->toBe('Rejected')
        ->and((int) $payment->fresh()->approved_by)->toBe($this->accountant->id)
        ->and($payment->fresh()->journal_entry_id)->toBeNull()
        ->and((float) $remittance->fresh()->short_amount)->toBe(3000.0)
        ->and((float) $remittance->fresh()->driver_amount_paid)->toBe(0.0);
});

test('missing boundary mappings roll back both approval and payment posting', function () {
    $remittance = boundaryPaymentTestRemittance(3000, 2000);
    AccountMapping::where('mapping_key', 'boundary_revenue')->delete();

    $this->post(route('remittance-approval.approve', $remittance), [
        'boundary_payment_date' => now()->toDateString(),
        'confirm_boundary_received' => '1',
    ])->assertSessionHas('error');

    expect($remittance->fresh()->status)->toBe('pending')
        ->and($remittance->boundaryPayments()->count())->toBe(0)
        ->and(JournalEntry::where('reference_type', 'Boundary Payment')->count())->toBe(0);
});

test('boundary journal entries keep their payment link when voided through the existing accounting workflow', function () {
    $remittance = boundaryPaymentTestRemittance(3000, 3000);

    $this->post(route('remittance-approval.approve', $remittance), [
        'boundary_payment_date' => now()->toDateString(),
        'confirm_boundary_received' => '1',
    ])->assertRedirect();

    $payment = $remittance->boundaryPayments()->firstOrFail();
    $journalEntry = $payment->journalEntry;

    $this->patch(route('accounting.journal-entries.void', $journalEntry))->assertRedirect();

    expect($journalEntry->fresh()->status)->toBe('Void')
        ->and((int) $journalEntry->fresh()->boundaryPayment->id)->toBe($payment->id)
        ->and((float) $remittance->fresh()->short_amount)->toBe(0.0)
        ->and(app(\App\Services\AccountingReportService::class)->postedTotals()->has($this->cash->id))->toBeFalse();
});
