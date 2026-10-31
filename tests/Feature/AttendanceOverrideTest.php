<?php

use App\Models\Attendance;
use App\Models\OvertimeUndertime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('removes all overtime and undertime records when an attendance is overridden without both times', function () {
    $hr = User::create([
        'name' => 'Attendance HR',
        'username' => 'attendance-hr',
        'password' => 'password',
        'role' => 'hr',
        'status' => 'active',
    ]);

    $employee = User::create([
        'name' => 'Attendance Employee',
        'username' => 'attendance-employee',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $attendance = Attendance::create([
        'user_id' => $employee->id,
        'date' => '2026-10-29 00:00:00',
        'time_in' => '07:30:00',
        'time_out' => '18:00:00',
        'status' => 'present',
    ]);

    foreach (['pending', 'approved', 'rejected'] as $status) {
        foreach (['overtime', 'undertime'] as $type) {
            DB::table('overtime_undertimes')->insert([
                'user_id' => $employee->id,
                'date' => '2026-10-29',
                'type' => $type,
                'hours' => 1,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    $otherDateRecordId = DB::table('overtime_undertimes')->insertGetId([
        'user_id' => $employee->id,
        'date' => '2026-10-28',
        'type' => 'overtime',
        'hours' => 1,
        'status' => 'approved',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($hr)
        ->put(route('attendance.update', $attendance), [
            'user_id' => $employee->id,
            'date' => '2026-10-29 00:00:00',
            'time_in' => null,
            'time_out' => null,
        ])
        ->assertRedirect(route('attendance.index'));

    $attendance->refresh();
    expect($attendance->time_in)->toBeNull()
        ->and($attendance->time_out)->toBeNull()
        ->and($attendance->status)->toBe('absent')
        ->and(OvertimeUndertime::where('user_id', $employee->id)
            ->whereDate('date', '2026-10-29')
            ->exists())->toBeFalse()
        ->and(OvertimeUndertime::whereKey($otherDateRecordId)->exists())->toBeTrue();
});
