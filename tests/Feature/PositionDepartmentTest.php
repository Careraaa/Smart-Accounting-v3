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
