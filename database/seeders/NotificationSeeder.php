<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $employeeIds = SeedConfig::employeeIds();
        $hrId = DB::table('users')->where('role', 'hr')->value('id');
        $acctId = DB::table('users')->where('role', 'accountant')->value('id');
        $seedUserIds = array_values(array_filter(array_merge($employeeIds, [$hrId, $acctId])));

        $seedTypes = [
            'payroll_created', 'payroll_released', 'leave_submitted', 'leave_approved',
            'leave_rejected', 'overtime_submitted', 'overtime_approved', 'attendance_recorded', 'system_tip',
        ];

        DB::table('notifications')->whereIn('user_id', $seedUserIds)->whereIn('type', $seedTypes)->delete();

        $samplePayroll = DB::table('payrolls')->orderByDesc('id')->first();
        $sampleLeave = DB::table('leaves')->orderByDesc('id')->first();
        $sampleOt = DB::table('overtime_undertimes')->orderByDesc('id')->first();

        $rows = [];
        $now = now();

        foreach (array_slice($employeeIds, 0, 8) as $empId) {
            $rows[] = [
                'user_id' => $empId,
                'type' => 'system_tip',
                'title' => 'Update your emergency contact',
                'message' => 'Please verify your phone number and address in your employee profile.',
                'data' => json_encode(['scope' => 'employee', 'source' => 'seeder']),
                'read_at' => null,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ];
        }

        if ($hrId) {
            if ($sampleLeave) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'leave_submitted',
                    'title' => 'Leave request pending review',
                    'message' => 'A leave request for Apr 2026 is waiting for HR action.',
                    'data' => json_encode(['leave_id' => $sampleLeave->id, 'employee_id' => $sampleLeave->user_id]),
                    'read_at' => null,
                    'created_at' => $now->copy()->subHours(18),
                    'updated_at' => $now->copy()->subHours(18),
                ];
            }
            if ($sampleOt) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'overtime_submitted',
                    'title' => 'OT/UT entries recorded',
                    'message' => 'Review approved overtime and undertime for the current cutoff.',
                    'data' => json_encode(['record_id' => $sampleOt->id, 'employee_id' => $sampleOt->user_id]),
                    'read_at' => null,
                    'created_at' => $now->copy()->subHours(12),
                    'updated_at' => $now->copy()->subHours(12),
                ];
            }
            if ($samplePayroll) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'payroll_created',
                    'title' => 'Payroll batch ready',
                    'message' => 'Apr 16–30 payroll batch is ready for review and finalization.',
                    'data' => json_encode(['payroll_id' => $samplePayroll->id]),
                    'read_at' => null,
                    'created_at' => $now->copy()->subHours(6),
                    'updated_at' => $now->copy()->subHours(6),
                ];
            }
            $rows[] = [
                'user_id' => $hrId,
                'type' => 'attendance_recorded',
                'title' => 'Attendance synced',
                'message' => 'Attendance data for Feb–Apr 2026 has been loaded for reporting.',
                'data' => json_encode(['range' => SeedConfig::RANGE_START . ' to ' . SeedConfig::RANGE_END]),
                'read_at' => null,
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(4),
            ];
        }

        if ($acctId && $samplePayroll) {
            $rows[] = [
                'user_id' => $acctId,
                'type' => 'payroll_released',
                'title' => 'Payroll awaiting approval',
                'message' => 'Submitted payroll batches from Q1 2026 demo data are ready for accountant sign-off.',
                'data' => json_encode(['payroll_id' => $samplePayroll->id]),
                'read_at' => null,
                'created_at' => $now->copy()->subHours(8),
                'updated_at' => $now->copy()->subHours(8),
            ];
        }

        DB::table('notifications')->insert($rows);
        $this->command?->info('NotificationSeeder: ' . count($rows) . ' notifications.');
    }
}
