<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeRelationsSeeder extends Seeder
{
    public function run(): void
    {
        $employees = DB::table('users')
            ->whereIn('id', SeedConfig::employeeIds())
            ->select('id', 'username', 'first_name', 'last_name', 'position', 'department', 'date_of_hire', 'spouse_name')
            ->get();

        if ($employees->isEmpty()) {
            $this->command?->warn('No employees found; skipping EmployeeRelationsSeeder.');
            return;
        }

        $ids = $employees->pluck('id')->all();
        DB::table('employee_experiences')->whereIn('user_id', $ids)->delete();
        DB::table('employee_skills')->whereIn('user_id', $ids)->delete();
        DB::table('employee_beneficiaries')->whereIn('user_id', $ids)->delete();
        DB::table('employee_references')->whereIn('user_id', $ids)->delete();

        $now = now();
        $experiences = [];
        $skills = [];
        $beneficiaries = [];
        $references = [];

        $companies = [
            'Metro Transit Cooperative', 'Bayan Logistics Services', 'QuickMart PH',
            'Sunrise Transport Inc.', 'National Freight Corp.',
        ];
        $skillPool = [
            ['MS Excel (Pivot/VLOOKUP)', 'Advanced'],
            ['Dispatch coordination', 'Intermediate'],
            ['Customer service', 'Advanced'],
            ['Fleet documentation', 'Intermediate'],
            ['Payroll encoding', 'Intermediate'],
            ['Safety compliance', 'Advanced'],
        ];

        foreach ($employees as $emp) {
            $hireYear = (int) substr((string) $emp->date_of_hire, 0, 4);
            $experiences[] = [
                'user_id' => $emp->id,
                'company_name' => $companies[$emp->id % count($companies)],
                'position' => $emp->position . ' (Junior)',
                'duration' => ($hireYear - 3) . '–' . ($hireYear - 1),
                'responsibilities' => 'Daily operations support, reporting, and team coordination.',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $s1 = $skillPool[$emp->id % count($skillPool)];
            $s2 = $skillPool[($emp->id + 2) % count($skillPool)];
            $skills[] = ['user_id' => $emp->id, 'skill_name' => $s1[0], 'proficiency' => $s1[1], 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now];
            $skills[] = ['user_id' => $emp->id, 'skill_name' => $s2[0], 'proficiency' => $s2[1], 'sequence' => 2, 'created_at' => $now, 'updated_at' => $now];

            if ($emp->spouse_name) {
                $beneficiaries[] = [
                    'user_id' => $emp->id,
                    'name' => $emp->spouse_name,
                    'date_of_birth' => '1990-01-15',
                    'relationship' => 'spouse',
                    'sequence' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $references[] = [
                'user_id' => $emp->id,
                'name' => 'Engr. Paulo Garcia',
                'address' => 'Quezon City, Metro Manila',
                'contact_number' => '0917-' . str_pad((string) (5000000 + $emp->id), 7, '0', STR_PAD_LEFT),
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('employee_experiences')->insert($experiences);
        DB::table('employee_skills')->insert($skills);
        if ($beneficiaries) {
            DB::table('employee_beneficiaries')->insert($beneficiaries);
        }
        DB::table('employee_references')->insert($references);

        $this->command?->info('EmployeeRelationsSeeder: experiences, skills, beneficiaries, and references for ' . $employees->count() . ' employees.');
    }
}
