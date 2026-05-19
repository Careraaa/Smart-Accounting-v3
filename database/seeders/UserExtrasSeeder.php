<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserExtrasSeeder extends Seeder
{
    public function run(): void
    {
        if (SchemaHas::column('users', 'password_changed')) {
            DB::table('users')->whereNull('password_changed')->update(['password_changed' => 1]);
        }

        if (SchemaHas::column('users', 'gender')) {
            foreach (SeedConfig::employeeProfiles() as $i => $p) {
                $id = SeedConfig::EMPLOYEE_ID_START + $i;
                DB::table('users')->where('id', $id)->update(['gender' => $p['gender']]);
            }

            foreach (['juan.delacruz' => 'male', 'mark.santos' => 'male', 'mary.grace' => 'female', 'qr_admin' => 'female'] as $username => $gender) {
                DB::table('users')->where('username', $username)->update(['gender' => $gender]);
            }
        }
    }
}

final class SchemaHas
{
    public static function column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }
}
