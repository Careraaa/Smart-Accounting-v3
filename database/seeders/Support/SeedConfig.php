<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Shared demo seed window: January 2025 through April 2026.
 */
final class SeedConfig
{
    public const PASSWORD_HASH = '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS';

    public const RANGE_START = '2025-01-01';

    public const RANGE_END = '2026-04-30';

    public const SEED_YEARS = [2025, 2026];

    /** First employee user id (after 5 system/staff accounts). */
    public const EMPLOYEE_ID_START = 6;

    public const EMPLOYEE_COUNT = 20;

    /** Holidays inside the seed window (no attendance / remittance on these dates). */
    public const HOLIDAYS_IN_RANGE = [
        '2025-01-01', // New Year
        '2025-02-25', // EDSA People Power
        '2025-04-09', // Araw ng Kagitingan
        '2026-01-01', // New Year
        '2026-02-25', // EDSA
        '2026-04-09', // Araw ng Kagitingan
    ];

    /**
     * Bi-monthly payroll periods covering Jan 2025 – Apr 2026.
     *
     * @return array<int, array{start: string, end: string, label: string}>
     */
    public static function payrollPeriods(): array
    {
        $periods = [];
        $cursor = Carbon::parse('2025-01-01')->startOfMonth();
        $end = Carbon::parse(self::RANGE_END);

        while ($cursor->lte($end)) {
            // First half: 1st – 15th
            $firstEnd = $cursor->copy()->day(15);
            if ($firstEnd->lte($end)) {
                $periods[] = [
                    'start' => $cursor->toDateString(),
                    'end' => $firstEnd->toDateString(),
                    'label' => $firstEnd->format('M j') . "\xe2\x80\x93" . '15',
                ];
            }

            // Second half: 16th – last day of month
            $secondStart = $cursor->copy()->day(16);
            $monthEnd = $cursor->copy()->endOfMonth();
            $secondEnd = $monthEnd->lte($end) ? $monthEnd : $end->copy();
            if ($secondStart->lte($end)) {
                $periods[] = [
                    'start' => $secondStart->toDateString(),
                    'end' => $secondEnd->toDateString(),
                    'label' => $secondStart->format('M j') . "\xe2\x80\x93" . $secondEnd->format('j'),
                ];
            }

            $cursor->addMonth();
        }

        return $periods;
    }

    /**
     * Weekdays between RANGE_START and RANGE_END, excluding seeded holidays.
     *
     * @return list<string>
     */
    public static function workDaysInRange(): array
    {
        $days = [];
        $holidaySet = array_flip(self::HOLIDAYS_IN_RANGE);

        foreach (CarbonPeriod::create(self::RANGE_START, self::RANGE_END) as $date) {
            if ($date->isWeekend()) {
                continue;
            }
            $iso = $date->toDateString();
            if (isset($holidaySet[$iso])) {
                continue;
            }
            $days[] = $iso;
        }

        return $days;
    }

    /**
     * @return list<string> Demo employee usernames (20).
     */
    public static function employeeUsernames(): array
    {
        return array_column(self::employeeProfiles(), 'username');
    }

    /**
     * @return list<int> User ids 6–25.
     */
    public static function employeeIds(): array
    {
        return range(self::EMPLOYEE_ID_START, self::EMPLOYEE_ID_START + self::EMPLOYEE_COUNT - 1);
    }

    /**
     * Realistic employee roster for a transport / logistics company.
     *
     * @return list<array<string, mixed>>
     */
    public static function employeeProfiles(): array
    {
        $profiles = [
            ['first' => 'John', 'middle' => 'Michael', 'last' => 'Reyes', 'username' => 'john.reyes', 'email' => 'john.reyes@gmail.com', 'phone' => '09171234001', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Ana Reyes', 'dob' => '1990-04-12', 'pob' => 'Quezon City', 'edu' => "College (Bachelor's)", 'hire' => '2022-08-15', 'position' => 'Fleet Manager', 'dept' => 'Operation', 'salary' => 800.00, 'street' => '42 Katipunan Ave.', 'barangay' => 'Brgy. Loyola Heights', 'city' => 'Quezon City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => true],
            ['first' => 'Angela', 'middle' => 'Marie', 'last' => 'Fernandez', 'username' => 'angela.fernandez', 'email' => 'angela.fernandez@gmail.com', 'phone' => '09181234002', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1994-08-15', 'pob' => 'Davao City', 'edu' => "College (Bachelor's)", 'hire' => '2023-02-01', 'position' => 'Administration Staff', 'dept' => 'Admin', 'salary' => 550.00, 'street' => '18 Mabini St.', 'barangay' => 'Brgy. Matina', 'city' => 'Davao City', 'province' => 'Davao del Sur', 'statutory' => false, 'dl' => false],
            ['first' => 'Roberto', 'middle' => 'Antonio', 'last' => 'Mendoza', 'username' => 'roberto.mendoza', 'email' => 'roberto.mendoza@gmail.com', 'phone' => '09191234003', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Carmen Mendoza', 'dob' => '1988-11-03', 'pob' => 'Manila', 'edu' => "College (Bachelor's)", 'hire' => '2020-06-01', 'position' => 'Logistics Supervisor', 'dept' => 'Admin', 'salary' => 620.00, 'street' => '7 Aurora Blvd.', 'barangay' => 'Brgy. Cubao', 'city' => 'Quezon City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Maria', 'middle' => 'Lourdes', 'last' => 'Santos', 'username' => 'maria.santos', 'email' => 'maria.santos@gmail.com', 'phone' => '09201234004', 'gender' => 'female', 'civil' => 'married', 'spouse' => 'Jose Santos', 'dob' => '1992-07-22', 'pob' => 'Pasig City', 'edu' => 'Senior High School', 'hire' => '2021-01-15', 'position' => 'Logistic Staff', 'dept' => 'Admin', 'salary' => 550.00, 'street' => '55 Ortigas Ave.', 'barangay' => 'Brgy. Kapitolyo', 'city' => 'Pasig City', 'province' => 'Metro Manila', 'statutory' => false, 'dl' => false],
            ['first' => 'Carlo', 'middle' => 'Enrique', 'last' => 'Dimaculangan', 'username' => 'carlo.dimaculangan', 'email' => 'carlo.dimaculangan@gmail.com', 'phone' => '09211234005', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Ligaya Dimaculangan', 'dob' => '1996-11-05', 'pob' => 'Marikina City', 'edu' => "College (Associate's)", 'hire' => '2022-03-10', 'position' => 'Dispatcher', 'dept' => 'Operation', 'salary' => 650.00, 'street' => '9 J.P. Rizal St.', 'barangay' => 'Brgy. Concepcion', 'city' => 'Marikina City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Patricia', 'middle' => 'Ann', 'last' => 'Villanueva', 'username' => 'patricia.villanueva', 'email' => 'patricia.villanueva@gmail.com', 'phone' => '09221234006', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1997-02-18', 'pob' => 'Makati City', 'edu' => "College (Bachelor's)", 'hire' => '2023-09-01', 'position' => 'HR Assistant', 'dept' => 'HR', 'salary' => 580.00, 'street' => '31 Ayala Ave. Ext.', 'barangay' => 'Brgy. San Lorenzo', 'city' => 'Makati City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Michael', 'middle' => null, 'last' => 'Tan', 'username' => 'michael.tan', 'email' => 'michael.tan@gmail.com', 'phone' => '09231234007', 'gender' => 'male', 'civil' => 'single', 'spouse' => null, 'dob' => '1993-05-30', 'pob' => 'Binondo, Manila', 'edu' => "College (Bachelor's)", 'hire' => '2021-11-08', 'position' => 'Accounting Staff', 'dept' => 'Accounting', 'salary' => 600.00, 'street' => '88 Ongpin St.', 'barangay' => 'Brgy. 289', 'city' => 'Manila', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Jennifer', 'middle' => 'Rose', 'last' => 'Cruz', 'username' => 'jennifer.cruz', 'email' => 'jennifer.cruz@gmail.com', 'phone' => '09241234008', 'gender' => 'female', 'civil' => 'married', 'spouse' => 'Mark Cruz', 'dob' => '1991-12-08', 'pob' => 'Caloocan City', 'edu' => "College (Bachelor's)", 'hire' => '2020-04-20', 'position' => 'Payroll Officer', 'dept' => 'Accounting', 'salary' => 620.00, 'street' => '14 Rizal Ave.', 'barangay' => 'Brgy. 12', 'city' => 'Caloocan City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Ricardo', 'middle' => 'Luis', 'last' => 'Almario', 'username' => 'ricardo.almario', 'email' => 'ricardo.almario@gmail.com', 'phone' => '09251234009', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Elena Almario', 'dob' => '1989-03-25', 'pob' => 'Bulacan', 'edu' => 'Senior High School', 'hire' => '2019-07-01', 'position' => 'Driver Coordinator', 'dept' => 'Operation', 'salary' => 580.00, 'street' => '22 MacArthur Hwy.', 'barangay' => 'Brgy. Tabang', 'city' => 'Guiguinto', 'province' => 'Bulacan', 'statutory' => true, 'dl' => true],
            ['first' => 'Grace', 'middle' => null, 'last' => 'Ong', 'username' => 'grace.ong', 'email' => 'grace.ong@gmail.com', 'phone' => '09261234010', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1998-06-14', 'pob' => 'Cebu City', 'edu' => "College (Associate's)", 'hire' => '2024-01-08', 'position' => 'Receptionist', 'dept' => 'Admin', 'salary' => 520.00, 'street' => '5 Escario St.', 'barangay' => 'Brgy. Kamagayan', 'city' => 'Cebu City', 'province' => 'Cebu', 'statutory' => false, 'dl' => false],
            ['first' => 'Fernando', 'middle' => 'Jose', 'last' => 'Garcia', 'username' => 'fernando.garcia', 'email' => 'fernando.garcia@gmail.com', 'phone' => '09271234011', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Luz Garcia', 'dob' => '1987-09-09', 'pob' => 'Laguna', 'edu' => 'Vocational / TESDA', 'hire' => '2018-05-14', 'position' => 'Lead Mechanic', 'dept' => 'Maintenance', 'salary' => 600.00, 'street' => '67 National Hwy.', 'barangay' => 'Brgy. San Antonio', 'city' => 'Biñan', 'province' => 'Laguna', 'statutory' => true, 'dl' => true],
            ['first' => 'Leah', 'middle' => 'Marie', 'last' => 'Bautista', 'username' => 'leah.bautista', 'email' => 'leah.bautista@gmail.com', 'phone' => '09281234012', 'gender' => 'female', 'civil' => 'married', 'spouse' => 'Ryan Bautista', 'dob' => '1990-01-27', 'pob' => 'Quezon City', 'edu' => "College (Bachelor's)", 'hire' => '2022-02-14', 'position' => 'Safety Officer', 'dept' => 'Operation', 'salary' => 610.00, 'street' => '3 Commonwealth Ave.', 'barangay' => 'Brgy. Batasan Hills', 'city' => 'Quezon City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Dominic', 'middle' => null, 'last' => 'Reyes', 'username' => 'dominic.reyes', 'email' => 'dominic.reyes@gmail.com', 'phone' => '09291234013', 'gender' => 'male', 'civil' => 'single', 'spouse' => null, 'dob' => '1995-10-02', 'pob' => 'Mandaluyong City', 'edu' => "College (Bachelor's)", 'hire' => '2023-06-12', 'position' => 'IT Support Specialist', 'dept' => 'Admin', 'salary' => 650.00, 'street' => '12 Shaw Blvd.', 'barangay' => 'Brgy. Wack-Wack', 'city' => 'Mandaluyong City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Sofia', 'middle' => 'Isabel', 'last' => 'Ramirez', 'username' => 'sofia.ramirez', 'email' => 'sofia.ramirez@gmail.com', 'phone' => '09301234014', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1994-04-19', 'pob' => 'Iloilo City', 'edu' => "College (Bachelor's)", 'hire' => '2021-08-23', 'position' => 'Bookkeeper', 'dept' => 'Accounting', 'salary' => 590.00, 'street' => '29 Delgado St.', 'barangay' => 'Brgy. Mabolo', 'city' => 'Iloilo City', 'province' => 'Iloilo', 'statutory' => true, 'dl' => false],
            ['first' => 'Noel', 'middle' => 'Victor', 'last' => 'Aquino', 'username' => 'noel.aquino', 'email' => 'noel.aquino@gmail.com', 'phone' => '09311234015', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Teresa Aquino', 'dob' => '1993-08-07', 'pob' => 'Pampanga', 'edu' => 'Senior High School', 'hire' => '2022-11-01', 'position' => 'Warehouse Clerk', 'dept' => 'Operation', 'salary' => 540.00, 'street' => '8 Jose Abad Santos Ave.', 'barangay' => 'Brgy. San Nicolas', 'city' => 'Angeles City', 'province' => 'Pampanga', 'statutory' => false, 'dl' => false],
            ['first' => 'Hannah', 'middle' => 'Joy', 'last' => 'Morales', 'username' => 'hannah.morales', 'email' => 'hannah.morales@gmail.com', 'phone' => '09321234016', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1999-03-11', 'pob' => 'Bacolod City', 'edu' => "College (Bachelor's)", 'hire' => '2024-05-06', 'position' => 'Customer Relations Officer', 'dept' => 'Admin', 'salary' => 560.00, 'street' => '41 Lacson St.', 'barangay' => 'Brgy. 7', 'city' => 'Bacolod City', 'province' => 'Negros Occidental', 'statutory' => false, 'dl' => false],
            ['first' => 'Jericho', 'middle' => null, 'last' => 'Lim', 'username' => 'jericho.lim', 'email' => 'jericho.lim@gmail.com', 'phone' => '09331234017', 'gender' => 'male', 'civil' => 'single', 'spouse' => null, 'dob' => '1992-12-30', 'pob' => 'Binondo, Manila', 'edu' => "College (Bachelor's)", 'hire' => '2020-10-05', 'position' => 'Route Planner', 'dept' => 'Operation', 'salary' => 630.00, 'street' => '6 Reina Regente St.', 'barangay' => 'Brgy. 287', 'city' => 'Manila', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Katrina', 'middle' => 'Anne', 'last' => 'Go', 'username' => 'katrina.go', 'email' => 'katrina.go@gmail.com', 'phone' => '09341234018', 'gender' => 'female', 'civil' => 'married', 'spouse' => 'Daniel Go', 'dob' => '1988-05-16', 'pob' => 'Makati City', 'edu' => "College (Bachelor's)", 'hire' => '2019-03-18', 'position' => 'Compliance Officer', 'dept' => 'HR', 'salary' => 680.00, 'street' => '90 Chino Roces Ave.', 'barangay' => 'Brgy. Palanan', 'city' => 'Makati City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Emmanuel', 'middle' => 'Dizon', 'last' => 'Del Rosario', 'username' => 'emmanuel.delrosario', 'email' => 'emmanuel.delrosario@gmail.com', 'phone' => '09351234019', 'gender' => 'male', 'civil' => 'married', 'spouse' => 'Claire Del Rosario', 'dob' => '1986-07-04', 'pob' => 'Quezon City', 'edu' => "College (Master's)", 'hire' => '2017-09-11', 'position' => 'Training Specialist', 'dept' => 'HR', 'salary' => 600.00, 'street' => '25 Timog Ave.', 'barangay' => 'Brgy. South Triangle', 'city' => 'Quezon City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
            ['first' => 'Alyssa', 'middle' => 'Marie', 'last' => 'Mariano', 'username' => 'alyssa.mariano', 'email' => 'alyssa.mariano@gmail.com', 'phone' => '09361234020', 'gender' => 'female', 'civil' => 'single', 'spouse' => null, 'dob' => '1996-09-23', 'pob' => 'Taguig City', 'edu' => "College (Bachelor's)", 'hire' => '2023-04-17', 'position' => 'Procurement Staff', 'dept' => 'Operation', 'salary' => 570.00, 'street' => '11 C-5 Road', 'barangay' => 'Brgy. Western Bicutan', 'city' => 'Taguig City', 'province' => 'Metro Manila', 'statutory' => true, 'dl' => false],
        ];

        return $profiles;
    }

    public static function formatSss(int $seq): string
    {
        return sprintf('34-%07d-%d', 2000000 + $seq, $seq % 10);
    }

    public static function formatTin(int $seq): string
    {
        return sprintf('%03d-%03d-%03d', 100 + ($seq % 900), $seq % 1000, $seq % 1000);
    }

    public static function formatPagibig(int $seq): string
    {
        return sprintf('%04d-%04d-%04d', 1000 + $seq, $seq % 10000, $seq % 10000);
    }

    public static function formatPhilhealth(int $seq): string
    {
        return sprintf('12-%010d-%d', 1000000000 + $seq, $seq % 10);
    }

    /** Deterministic pseudo-random float 0–1 from user id + salt. */
    public static function hashFloat(int $userId, string $salt): float
    {
        $h = crc32($userId . '|' . $salt);

        return ($h & 0x7fffffff) / 0x7fffffff;
    }
}
