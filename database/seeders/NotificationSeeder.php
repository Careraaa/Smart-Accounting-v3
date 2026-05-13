<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')
            ->whereIn('username', ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->orWhereIn('role', ['hr', 'accountant'])
            ->select('id', 'username', 'role')
            ->get();

        $hrId = $users->firstWhere('role', 'hr')->id ?? null;
        $acctId = $users->firstWhere('role', 'accountant')->id ?? null;

        $demoEmployees = $users->whereIn('username', ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga']);
        if ($demoEmployees->isEmpty()) {
            $this->command?->warn('No demo employee users found; skipping NotificationSeeder.');
            return;
        }

        $employeeIds = $demoEmployees->pluck('id')->values()->all();
        $seedUserIds = array_values(array_filter(array_merge($employeeIds, [$hrId, $acctId])));

        // Keep it idempotent: remove only notifications we seed (by type prefix list)
        $seedTypes = [
            'payroll_created',
            'payroll_released',
            'leave_submitted',
            'leave_approved',
            'leave_rejected',
            'overtime_submitted',
            'overtime_approved',
            'overtime_rejected',
            'attendance_recorded',
            'system_tip',
        ];

        DB::table('notifications')
            ->whereIn('user_id', $seedUserIds)
            ->whereIn('type', $seedTypes)
            ->delete();

        $now = now();

        // Pull a few seeded records to reference IDs in notification payloads
        $samplePayroll = DB::table('payrolls')->orderByDesc('id')->first();
        $sampleLeave = DB::table('leaves')->orderByDesc('id')->first();
        $sampleOt = DB::table('overtime_undertimes')->orderByDesc('id')->first();

        $rows = [];

        // Employee-facing “FYI” notifications (no HR-only routes)
        foreach ($demoEmployees as $emp) {
            $rows[] = [
                'user_id' => $emp->id,
                'type' => 'system_tip',
                'title' => 'Tip: Keep your profile updated',
                'message' => 'Update your contact number and address para mabilis ang HR coordination.',
                'data' => json_encode(['scope' => 'employee', 'source' => 'seeder']),
                'read_at' => null,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ];
        }

        // HR notifications (links resolve via Notification::getActionUrl)
        if ($hrId) {
            if ($sampleLeave) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'leave_submitted',
                    'title' => 'New leave request submitted',
                    'message' => 'May bagong leave request na kailangan i-review.',
                    'data' => json_encode(['leave_id' => $sampleLeave->id, 'employee_id' => $sampleLeave->user_id]),
                    'read_at' => null,
                    'created_at' => $now->copy()->subHours(20),
                    'updated_at' => $now->copy()->subHours(20),
                ];
            }

            if ($sampleOt) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'overtime_submitted',
                    'title' => 'New OT/UT request submitted',
                    'message' => 'Please check OT/UT request details for approval.',
                    'data' => json_encode(['record_id' => $sampleOt->id, 'employee_id' => $sampleOt->user_id]),
                    'read_at' => null,
                    'created_at' => $now->copy()->subHours(16),
                    'updated_at' => $now->copy()->subHours(16),
                ];
            }

            if ($samplePayroll) {
                $rows[] = [
                    'user_id' => $hrId,
                    'type' => 'payroll_created',
                    'title' => 'Payroll batch generated',
                    'message' => 'Payroll batch is ready for review. Add employees, prepare, and finalize to submit.',
                    'data' => json_encode(['payroll_id' => $samplePayroll->id]),
                    'read_at' => $now->copy()->subHours(2),
                    'created_at' => $now->copy()->subHours(10),
                    'updated_at' => $now->copy()->subHours(2),
                ];
            }

            $rows[] = [
                'user_id' => $hrId,
                'type' => 'attendance_recorded',
                'title' => 'Attendance logs updated',
                'message' => 'Attendance records have been updated for the current cutoff period.',
                'data' => json_encode(['scope' => 'attendance', 'source' => 'seeder']),
                'read_at' => null,
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6),
            ];
        }

        // Accountant notifications (approval queue)
        if ($acctId && $samplePayroll) {
            $rows[] = [
                'user_id' => $acctId,
                'type' => 'payroll_released',
                'title' => 'Payroll ready for approval',
                'message' => 'May payroll na ready i-approve. Please review deductions and totals.',
                'data' => json_encode(['payroll_id' => $samplePayroll->id]),
                'read_at' => null,
                'created_at' => $now->copy()->subHours(8),
                'updated_at' => $now->copy()->subHours(8),
            ];
        }

        DB::table('notifications')->insert($rows);
        $this->command?->info('✅ NotificationSeeder: seeded ' . count($rows) . ' notifications.');
    }
}

