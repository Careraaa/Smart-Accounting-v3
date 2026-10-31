<?php

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows only gender-eligible leave types and balances to employees', function () {
    $female = User::create([
        'name' => 'Female Employee',
        'username' => 'female-leave-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'gender' => 'female',
    ]);
    $male = User::create([
        'name' => 'Male Employee',
        'username' => 'male-leave-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'gender' => 'male',
    ]);

    foreach (['Maternity Leave', 'Paternity Leave', 'Sick Leave'] as $name) {
        LeaveType::create([
            'name' => $name,
            'days_allowed' => 10,
            'status' => 'active',
        ]);
    }

    $this->actingAs($female)
        ->get(route('employee.leaves.create'))
        ->assertOk()
        ->assertSee('Maternity Leave')
        ->assertDontSee('Paternity Leave')
        ->assertSee('Sick Leave');

    $this->actingAs($female)
        ->get(route('employee.leaves.index'))
        ->assertOk()
        ->assertSee('Maternity Leave')
        ->assertDontSee('Paternity Leave');

    $this->actingAs($male)
        ->get(route('employee.leaves.create'))
        ->assertOk()
        ->assertSee('Paternity Leave')
        ->assertDontSee('Maternity Leave')
        ->assertSee('Sick Leave');

    $this->actingAs($male)
        ->get(route('employee.leaves.index'))
        ->assertOk()
        ->assertSee('Paternity Leave')
        ->assertDontSee('Maternity Leave');
});

it('rejects employee submissions for gender-restricted leave types', function () {
    $female = User::create([
        'name' => 'Female Employee',
        'username' => 'female-leave-requester',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'gender' => 'female',
    ]);
    $paternityLeave = LeaveType::create([
        'name' => 'Paternity Leave',
        'days_allowed' => 10,
        'status' => 'active',
    ]);

    $this->actingAs($female)
        ->post(route('employee.leaves.store'), [
            'leave_type_id' => $paternityLeave->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Test request',
        ])
        ->assertSessionHasErrors('leave_type_id');

    $this->assertDatabaseCount('leaves', 0);
});

it('rejects HR-created leave requests that do not match the employee gender', function () {
    $hr = User::create([
        'name' => 'HR User',
        'username' => 'hr-leave-manager',
        'password' => 'password',
        'role' => 'hr',
        'status' => 'active',
    ]);
    $male = User::create([
        'name' => 'Male Employee',
        'username' => 'male-hr-leave-target',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
        'gender' => 'male',
    ]);

    $this->actingAs($hr)
        ->post(route('leave.store'), [
            'user_id' => $male->id,
            'leave_type' => 'Maternity Leave',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Test request',
        ])
        ->assertSessionHasErrors('leave_type');

    $this->assertDatabaseCount('leaves', 0);
});
