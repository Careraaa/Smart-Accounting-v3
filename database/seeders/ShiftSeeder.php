<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create default 8am - 5pm shift if it doesn't exist
        Shift::firstOrCreate(
            ['name' => 'Office Hours'],
            [
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_duration' => '01:00:00', // 1 hour lunch break
                'is_active' => true,
            ]
        );
    }
}
