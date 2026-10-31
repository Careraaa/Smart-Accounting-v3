<?php

use App\Models\LeaveType;

it('restricts maternity and paternity leave to the matching gender', function () {
    expect(LeaveType::isNameAvailableForGender('Maternity Leave', 'female'))->toBeTrue()
        ->and(LeaveType::isNameAvailableForGender('Maternity Leave', 'male'))->toBeFalse()
        ->and(LeaveType::isNameAvailableForGender('Paternity Leave', 'male'))->toBeTrue()
        ->and(LeaveType::isNameAvailableForGender('Paternity Leave', 'female'))->toBeFalse();
});

it('leaves other leave types available to any gender', function () {
    expect(LeaveType::isNameAvailableForGender('Sick Leave', 'female'))->toBeTrue()
        ->and(LeaveType::isNameAvailableForGender('Sick Leave', 'male'))->toBeTrue()
        ->and(LeaveType::isNameAvailableForGender('Sick Leave', null))->toBeTrue();
});
