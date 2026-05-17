<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserExtrasSeeder extends Seeder
{
    public function run(): void
    {
        // Add extra info without changing names/roles/usernames.
        // Safe updates only where fields are null/empty.

        // Mark all seeded users as password already changed (so app doesn't force change flows)
        if (SchemaHas::column('users', 'password_changed')) {
            DB::table('users')->whereNull('password_changed')->update(['password_changed' => 1]);
        }

        // Set gender for demo employee accounts (Filipino context)
        if (SchemaHas::column('users', 'gender')) {
            $updates = [
                'john.doe'        => 'male',
                'angela.fernandez'=> 'female',
                'juan.trabaho'    => 'male',
                'maria.halos'     => 'female',
                'carlo.pahinga'   => 'male',
            ];

            foreach ($updates as $username => $gender) {
                DB::table('users')
                    ->where('username', $username)
                    ->where(function ($q) {
                        $q->whereNull('gender')->orWhere('gender', '');
                    })
                    ->update(['gender' => $gender]);
            }
        }

        // Add some IDs/contribution numbers for realism (only if missing)
        $numUpdates = [
            'john.doe' => [
                'has_sss' => 1, 'sss_number' => '34-5555555-5',
                'has_tin' => 1, 'tin_number' => '555-555-555',
                'has_pagibig' => 1, 'pagibig_number' => '5555-5555-5555',
                'has_philhealth' => 1, 'philhealth_number' => '55-5555555555-5',
            ],
        ];

        foreach ($numUpdates as $username => $data) {
            $row = DB::table('users')->where('username', $username)->first();
            if (!$row) continue;

            $patch = [];
            foreach ($data as $k => $v) {
                if (!SchemaHas::column('users', $k)) continue;
                if (!property_exists($row, $k) || $row->{$k} === null || $row->{$k} === '' || $row->{$k} === 0) {
                    $patch[$k] = $v;
                }
            }
            if ($patch) {
                DB::table('users')->where('username', $username)->update($patch);
            }
        }
    }
}

/**
 * Tiny helper so seeders can be resilient across migrations.
 */
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

