<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeRelationsSeeder extends Seeder
{
    public function run(): void
    {
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->whereIn('username', ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->select('id', 'username', 'first_name', 'last_name')
            ->get()
            ->keyBy('username');

        if ($employees->isEmpty()) {
            $this->command?->warn('No demo employees found; skipping EmployeeRelationsSeeder.');
            return;
        }

        $ids = $employees->pluck('id')->values()->all();

        DB::table('employee_experiences')->whereIn('user_id', $ids)->delete();
        DB::table('employee_skills')->whereIn('user_id', $ids)->delete();
        DB::table('employee_beneficiaries')->whereIn('user_id', $ids)->delete();
        DB::table('employee_references')->whereIn('user_id', $ids)->delete();

        $now = now();

        // Work experiences
        $experiences = [
            [
                'user_id' => $employees['john.doe']->id,
                'company_name' => 'Bayan Logistics Services',
                'position' => 'Operations Assistant',
                'duration' => '2019–2021',
                'responsibilities' => 'Dispatch support, route coordination, daily reports.',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['juan.trabaho']->id,
                'company_name' => 'Metro Transit Cooperative',
                'position' => 'Logistics Coordinator',
                'duration' => '2018–2021',
                'responsibilities' => 'Fleet scheduling, incident logs, payroll time summaries.',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['maria.halos']->id,
                'company_name' => 'QuickMart PH',
                'position' => 'Store Admin',
                'duration' => '2020–2023',
                'responsibilities' => 'Inventory encoding, cashier back office, schedules.',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('employee_experiences')->insert($experiences);

        // Special skills
        $skills = [
            ['user_id' => $employees['john.doe']->id, 'skill_name' => 'MS Excel (VLOOKUP/Pivot)', 'proficiency' => 'Advanced', 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $employees['john.doe']->id, 'skill_name' => 'Team coordination', 'proficiency' => 'Intermediate', 'sequence' => 2, 'created_at' => $now, 'updated_at' => $now],

            ['user_id' => $employees['angela.fernandez']->id, 'skill_name' => 'Document processing', 'proficiency' => 'Advanced', 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $employees['angela.fernandez']->id, 'skill_name' => 'Customer service', 'proficiency' => 'Intermediate', 'sequence' => 2, 'created_at' => $now, 'updated_at' => $now],

            ['user_id' => $employees['juan.trabaho']->id, 'skill_name' => 'Report writing', 'proficiency' => 'Expert', 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $employees['juan.trabaho']->id, 'skill_name' => 'Time management', 'proficiency' => 'Expert', 'sequence' => 2, 'created_at' => $now, 'updated_at' => $now],

            ['user_id' => $employees['maria.halos']->id, 'skill_name' => 'Data entry', 'proficiency' => 'Advanced', 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $employees['carlo.pahinga']->id, 'skill_name' => 'Dispatch coordination', 'proficiency' => 'Intermediate', 'sequence' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('employee_skills')->insert($skills);

        // Beneficiaries
        $beneficiaries = [
            [
                'user_id' => $employees['juan.trabaho']->id,
                'name' => 'Rosa Trabaho',
                'date_of_birth' => '1996-09-11',
                'relationship' => 'spouse',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['juan.trabaho']->id,
                'name' => 'Miguel Trabaho',
                'date_of_birth' => '2021-01-20',
                'relationship' => 'child',
                'sequence' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['maria.halos']->id,
                'name' => 'Roberto Halos',
                'date_of_birth' => '1991-03-05',
                'relationship' => 'spouse',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('employee_beneficiaries')->insert($beneficiaries);

        // Character references
        $refs = [
            [
                'user_id' => $employees['john.doe']->id,
                'name' => 'Arnel Bautista',
                'address' => 'Brgy. Commonwealth, Quezon City',
                'contact_number' => '0917-555-0101',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['angela.fernandez']->id,
                'name' => 'Ma. Theresa “Tess” Reyes',
                'address' => 'Matina, Davao City',
                'contact_number' => '0918-222-0202',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $employees['juan.trabaho']->id,
                'name' => 'Engr. Paulo Garcia',
                'address' => 'Cubao, Quezon City',
                'contact_number' => '0920-333-0303',
                'sequence' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('employee_references')->insert($refs);

        $this->command?->info('✅ EmployeeRelationsSeeder: seeded work exp, skills, beneficiaries, and references.');
    }
}

