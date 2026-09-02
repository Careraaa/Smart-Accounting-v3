<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinimalUsersSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $timestamp = now();

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Super Admin',
            'username' => 'super_admin',
            'first_name' => 'Super',
            'middle_name' => null,
            'last_name' => 'Admin',
            'password' => SeedConfig::PASSWORD_HASH,
            'role' => 'superadmin',
            'status' => 'active',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'QR Attendance Admin',
            'username' => 'qr_admin',
            'first_name' => 'QR',
            'middle_name' => null,
            'last_name' => 'Admin',
            'password' => SeedConfig::PASSWORD_HASH,
            'role' => 'qr_admin',
            'status' => 'active',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $this->command?->info('MinimalUsersSeeder: 1 superadmin and 1 QR admin user.');
    }
}