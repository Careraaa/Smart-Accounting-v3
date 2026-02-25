<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([Users_Seeder::class]);
        $this->call([StatutoryDeductions_Seeder::class]);
        $this->call([Drivers_Seeder::class]);
        $this->call([Pao_Seeder::class]);
        $this->call([Employees_Seeder::class]);
    }
}
