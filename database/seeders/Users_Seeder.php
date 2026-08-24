<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users_Seeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rows = array_merge(
            $this->systemAndStaffUsers(),
            $this->employeeUsers()
        );

        DB::table('users')->insert($rows);
        $this->command?->info('Users_Seeder: ' . count($rows) . ' users (' . SeedConfig::EMPLOYEE_COUNT . ' employees).');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function systemAndStaffUsers(): array
    {
        $pw = SeedConfig::PASSWORD_HASH;
        $ts = '2025-01-01 08:00:00';

        return [
            [
                'id' => 1, 'name' => 'Super Admin', 'username' => 'super_admin', 'password' => $pw,
                'role' => 'superadmin', 'profile_picture' => null, 'remember_token' => 'SuperAdminToken01',
                'first_name' => 'Super', 'middle_name' => null, 'last_name' => 'Admin',
                'email' => 'superadmin@gmail.com', 'phone' => null, 'address' => null,
                'civil_status' => null, 'spouse_name' => null, 'date_of_birth' => null, 'place_of_birth' => null,
                'educational_attainment' => null, 'driver_license_number' => null, 'driver_license_validity' => null,
                'date_of_hire' => null, 'position' => null, 'department' => null, 'status' => 'active',
                'salary_rate' => 0, 'work_days_per_week' => 5, 'has_sss' => 0, 'sss_number' => null, 'has_tin' => 0, 'tin_number' => null,
                'has_pagibig' => 0, 'pagibig_number' => null, 'has_philhealth' => 0, 'philhealth_number' => null,
                'signature_path' => null, 'attachments' => null, 'created_at' => $ts, 'updated_at' => $ts,
            ],
            [
                'id' => 2, 'name' => 'QR Attendance Admin', 'username' => 'qr_admin', 'password' => $pw,
                'role' => 'qr_admin', 'profile_picture' => null, 'remember_token' => 'QRAdminTkn01',
                'first_name' => 'Rica', 'middle_name' => null, 'last_name' => 'Navarro',
                'email' => 'qr.admin@gmail.com', 'phone' => '09131234567',
                'address' => json_encode(['street' => '50 EDSA', 'barangay' => 'Brgy. Highway Hills', 'city' => 'Mandaluyong City', 'province' => 'Metro Manila']),
                'civil_status' => 'single', 'spouse_name' => null, 'date_of_birth' => '1991-04-02', 'place_of_birth' => 'Mandaluyong City',
                'educational_attainment' => "College (Bachelor's)", 'driver_license_number' => null, 'driver_license_validity' => null,
                'date_of_hire' => '2024-06-01', 'position' => 'Attendance Administrator', 'department' => 'Admin', 'status' => 'active',
                'salary_rate' => 0, 'work_days_per_week' => 5, 'has_sss' => 0, 'sss_number' => null, 'has_tin' => 0, 'tin_number' => null,
                'has_pagibig' => 0, 'pagibig_number' => null, 'has_philhealth' => 0, 'philhealth_number' => null,
                'signature_path' => null, 'attachments' => null, 'created_at' => $ts, 'updated_at' => $ts,
            ],
            [
                'id' => 3, 'name' => 'Juan Dela Cruz', 'username' => 'juan.delacruz', 'password' => $pw,
                'role' => 'hr', 'profile_picture' => null, 'remember_token' => 'PD8ffT4qLQ',
                'first_name' => 'Juan', 'middle_name' => 'Rizal', 'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@gmail.com', 'phone' => '09101234567',
                'address' => json_encode(['street' => '100 Admin Street', 'barangay' => 'Brgy. Central', 'city' => 'Quezon City', 'province' => 'Metro Manila']),
                'civil_status' => 'married', 'spouse_name' => 'Maria Dela Cruz', 'date_of_birth' => '1985-02-10', 'place_of_birth' => 'Manila',
                'educational_attainment' => "College (Bachelor's)", 'driver_license_number' => null, 'driver_license_validity' => null,
                'date_of_hire' => '2018-01-01', 'position' => 'Chief Human Resources Officer', 'department' => 'HR', 'status' => 'active',
                'salary_rate' => 1000.00, 'work_days_per_week' => 5, 'has_sss' => 1, 'sss_number' => '34-1000001-1', 'has_tin' => 1, 'tin_number' => '100-000-001',
                'has_pagibig' => 1, 'pagibig_number' => '1000-0000-0001', 'has_philhealth' => 1, 'philhealth_number' => '12-0000000001-1',
                'signature_path' => null, 'attachments' => null, 'created_at' => $ts, 'updated_at' => $ts,
            ],
            [
                'id' => 4, 'name' => 'Mark Santos', 'username' => 'mark.santos', 'password' => $pw,
                'role' => 'remittance_clerk', 'profile_picture' => null, 'remember_token' => '26TRckTpBv',
                'first_name' => 'Mark', 'middle_name' => 'Anthony', 'last_name' => 'Santos',
                'email' => 'mark.santos@gmail.com', 'phone' => '09111234567',
                'address' => json_encode(['street' => '200 Clerk Street', 'barangay' => 'Brgy. Poblacion', 'city' => 'Manila', 'province' => 'Metro Manila']),
                'civil_status' => 'single', 'spouse_name' => null, 'date_of_birth' => '1993-06-15', 'place_of_birth' => 'Manila',
                'educational_attainment' => "College (Bachelor's)", 'driver_license_number' => 'N03-24-987654', 'driver_license_validity' => '2028-06-15',
                'date_of_hire' => '2021-03-15', 'position' => 'Remittance Clerk', 'department' => 'Operation', 'status' => 'active',
                'salary_rate' => 700.00, 'work_days_per_week' => 5, 'has_sss' => 1, 'sss_number' => '34-1000002-2', 'has_tin' => 1, 'tin_number' => '100-000-002',
                'has_pagibig' => 1, 'pagibig_number' => '1000-0000-0002', 'has_philhealth' => 1, 'philhealth_number' => '12-0000000002-2',
                'signature_path' => null, 'attachments' => null, 'created_at' => $ts, 'updated_at' => $ts,
            ],
            [
                'id' => 5, 'name' => 'Mary Grace Piattos', 'username' => 'mary.grace', 'password' => $pw,
                'role' => 'accountant', 'profile_picture' => null, 'remember_token' => 'K0ve6RLqfM',
                'first_name' => 'Mary Grace', 'middle_name' => 'Luna', 'last_name' => 'Piattos',
                'email' => 'mary.grace@gmail.com', 'phone' => '09121234567',
                'address' => json_encode(['street' => '300 Accounting Street', 'barangay' => 'Brgy. San Antonio', 'city' => 'Makati City', 'province' => 'Metro Manila']),
                'civil_status' => 'married', 'spouse_name' => 'Luis Piattos', 'date_of_birth' => '1990-09-20', 'place_of_birth' => 'Cebu City',
                'educational_attainment' => "College (Bachelor's)", 'driver_license_number' => null, 'driver_license_validity' => null,
                'date_of_hire' => '2019-06-01', 'position' => 'Chief Financial Officer', 'department' => 'Accounting', 'status' => 'active',
                'salary_rate' => 950.00, 'work_days_per_week' => 5, 'has_sss' => 1, 'sss_number' => '34-1000003-3', 'has_tin' => 1, 'tin_number' => '100-000-003',
                'has_pagibig' => 1, 'pagibig_number' => '1000-0000-0003', 'has_philhealth' => 1, 'philhealth_number' => '12-0000000003-3',
                'signature_path' => null, 'attachments' => null, 'created_at' => $ts, 'updated_at' => $ts,
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function employeeUsers(): array
    {
        $pw = SeedConfig::PASSWORD_HASH;
        $rows = [];
        $id = SeedConfig::EMPLOYEE_ID_START;

        foreach (SeedConfig::employeeProfiles() as $i => $p) {
            $seq = $i + 1;
            $hasStat = (bool) $p['statutory'];
            $name = trim($p['first'] . ' ' . ($p['middle'] ? $p['middle'] . ' ' : '') . $p['last']);
            $dlNum = !empty($p['dl']) ? sprintf('N04-%02d-%06d', 20 + $seq, 100000 + $seq) : null;
            $dlValid = $dlNum ? '2027-12-31' : null;

            $rows[] = [
                'id' => $id,
                'name' => $name,
                'username' => $p['username'],
                'password' => $pw,
                'role' => 'employee',
                'profile_picture' => null,
                'remember_token' => 'EmpTkn' . str_pad((string) $id, 4, '0', STR_PAD_LEFT),
                'first_name' => $p['first'],
                'middle_name' => $p['middle'],
                'last_name' => $p['last'],
                'email' => $p['email'],
                'phone' => $p['phone'],
                'address' => json_encode([
                    'street' => $p['street'],
                    'barangay' => $p['barangay'],
                    'city' => $p['city'],
                    'province' => $p['province'],
                ]),
                'civil_status' => $p['civil'],
                'spouse_name' => $p['spouse'],
                'date_of_birth' => $p['dob'],
                'place_of_birth' => $p['pob'],
                'educational_attainment' => $p['edu'],
                'driver_license_number' => $dlNum,
                'driver_license_validity' => $dlValid,
                'date_of_hire' => $p['hire'],
                'position' => $p['position'],
                'department' => $p['dept'],
                'status' => 'active',
                'salary_rate' => $p['salary'],
                'work_days_per_week' => $p['work_days'] ?? 5,
                'has_sss' => $hasStat ? 1 : 0,
                'sss_number' => $hasStat ? SeedConfig::formatSss($seq) : null,
                'has_tin' => $hasStat ? 1 : 0,
                'tin_number' => $hasStat ? SeedConfig::formatTin($seq) : null,
                'has_pagibig' => $hasStat ? 1 : 0,
                'pagibig_number' => $hasStat ? SeedConfig::formatPagibig($seq) : null,
                'has_philhealth' => $hasStat ? 1 : 0,
                'philhealth_number' => $hasStat ? SeedConfig::formatPhilhealth($seq) : null,
                'signature_path' => null,
                'attachments' => null,
                'created_at' => '2025-01-01 08:00:01',
                'updated_at' => '2025-01-01 08:00:01',
            ];
            $id++;
        }

        return $rows;
    }
}
