<?php

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('positions can be scoped by department', function () {
    $hrDepartment = App\Models\Department::create(['name' => 'HR']);
    $financeDepartment = App\Models\Department::create(['name' => 'Finance']);

    App\Models\PositionRate::create([
        'name' => 'HR Staff',
        'daily_rate' => 600.00,
        'department_id' => $hrDepartment->id,
        'is_active' => true,
    ]);

    App\Models\PositionRate::create([
        'name' => 'Accountant',
        'daily_rate' => 650.00,
        'department_id' => $financeDepartment->id,
        'is_active' => true,
    ]);

    $positions = App\Models\PositionRate::forDepartment($hrDepartment)->get();

    expect($positions)->toHaveCount(1)
        ->and($positions->first()->name)->toBe('HR Staff');
});

test('updating a position daily rate updates assigned employees and their detail page', function () {
    $department = App\Models\Department::create(['name' => 'HR']);
    $position = App\Models\PositionRate::create([
        'name' => 'HR Staff',
        'daily_rate' => 600.00,
        'department_id' => $department->id,
        'is_active' => true,
    ]);

    $admin = App\Models\User::create([
        'name' => 'Super Admin',
        'username' => 'position-rate-admin',
        'password' => 'password',
        'role' => 'superadmin',
        'status' => 'active',
    ]);
    $employee = App\Models\User::create([
        'name' => 'Test Employee',
        'username' => 'position-rate-employee',
        'password' => 'password',
        'role' => 'employee',
        'first_name' => 'Test',
        'last_name' => 'Employee',
        'position' => 'HR Staff',
        'department' => 'HR',
        'salary_rate' => 600.00,
        'status' => 'active',
    ]);
    $otherEmployee = App\Models\User::create([
        'name' => 'Other Employee',
        'username' => 'other-position-employee',
        'password' => 'password',
        'role' => 'employee',
        'position' => 'Driver',
        'salary_rate' => 500.00,
        'status' => 'active',
    ]);

    $this->actingAs($admin)
        ->put(route('settings.position.update', $position), [
            'name' => 'HR Staff',
            'daily_rate' => 750.00,
            'department_id' => $department->id,
        ])
        ->assertRedirect(route('configuration.index', ['tab' => 'positions']));

    $this->assertDatabaseHas('users', [
        'id' => $employee->id,
        'salary_rate' => 750.00,
    ]);
    $this->assertDatabaseHas('users', [
        'id' => $otherEmployee->id,
        'salary_rate' => 500.00,
    ]);

    $this->get(route('employees.show', ['employee' => $employee, 'tab' => 'employment']))
        ->assertOk()
        ->assertSee('₱750.00');
});
