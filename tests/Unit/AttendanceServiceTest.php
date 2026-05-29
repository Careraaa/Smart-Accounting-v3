<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\Shift;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\TestCase;

class AttendanceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Model::setConnectionResolver($capsule->getDatabaseManager());

        $capsule->schema()->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        $capsule->table('settings')->insert([
            'key' => 'attendance.grace_period_minutes',
            'value' => '5',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function test_underTime_should_not_deduct_break_when_attendance_ends_before_break_start()
    {
        $attendance = new Attendance();
        $attendance->date = Carbon::create(2026, 5, 29);
        $attendance->time_in = '08:00:00';
        $attendance->time_out = '12:00:00';

        $shift = new Shift();
        $shift->start_time = '08:00:00';
        $shift->end_time = '17:00:00';
        $shift->break_start = '12:00:00';
        $shift->break_end = '13:00:00';

        $service = new AttendanceService();
        $result = $service->calculateOvertimeAndUndertime($attendance, $shift);

        $this->assertEquals(4.0, $result['actual_hours']);
        $this->assertEquals(4.0, $result['undertime_hours']);
    }

    public function test_undertime_should_be_four_hours_when_attendance_spans_break_from_8_to_1()
    {
        $attendance = new Attendance();
        $attendance->date = Carbon::create(2026, 5, 29);
        $attendance->time_in = '08:00:00';
        $attendance->time_out = '13:00:00';

        $shift = new Shift();
        $shift->start_time = '08:00:00';
        $shift->end_time = '17:00:00';
        $shift->break_start = '12:00:00';
        $shift->break_end = '13:00:00';

        $service = new AttendanceService();
        $result = $service->calculateOvertimeAndUndertime($attendance, $shift);

        $this->assertEquals(4.0, $result['actual_hours']);
        $this->assertEquals(4.0, $result['undertime_hours']);
    }
}
