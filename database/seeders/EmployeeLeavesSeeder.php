<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeLeavesSeeder extends Seeder
{
    public function run(): void
    {
        $employees = DB::table('users')
            ->where('role', 'employee')
            ->select('id', 'username')
            ->get()
            ->keyBy('username');

        $hr = DB::table('users')->where('role', 'hr')->orderBy('id')->first();
        $hrId = $hr->id ?? null;

        if ($employees->isEmpty()) {
            $this->command?->warn('No employee users found; skipping EmployeeLeavesSeeder.');
            return;
        }

        $usernames = ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'];
        $targetIds = $employees->only($usernames)->pluck('id')->values()->all();
        if (!$targetIds) {
            $this->command?->warn('Demo employee users not found; skipping EmployeeLeavesSeeder.');
            return;
        }

        // Get leave type IDs from database
        $leaveTypes = DB::table('leave_types')
            ->whereIn('name', ['Vacation Leave', 'Sick Leave', 'Emergency Leave', 'Bereavement Leave'])
            ->pluck('id', 'name');

        if ($leaveTypes->isEmpty()) {
            $this->command?->warn('Leave types not found; skipping EmployeeLeavesSeeder.');
            return;
        }

        // Clear only the demo employees' seeded leaves (keeps real data safe)
        DB::table('leaves')->whereIn('user_id', $targetIds)->delete();

        $now = now();
        $rows = [];

        $rows[] = [
            'user_id' => $employees['juan.trabaho']->id ?? $targetIds[0],
            'leave_type_id' => $leaveTypes['Vacation Leave'] ?? 1,
            'leave_type' => 'Vacation Leave',
            'start_date' => '2026-03-23',
            'end_date' => '2026-03-25',
            'reason' => "VL muna — family out of town (Laguna). Will endorse tasks before leaving.",
            'status' => 'approved',
            'approved_by' => $hrId,
            'rejection_reason' => null,
            'created_at' => $now->copy()->subDays(30),
            'updated_at' => $now->copy()->subDays(28),
        ];

        $rows[] = [
            'user_id' => $employees['maria.halos']->id ?? $targetIds[0],
            'leave_type_id' => $leaveTypes['Sick Leave'] ?? 2,
            'leave_type' => 'Sick Leave',
            'start_date' => '2026-03-18',
            'end_date' => '2026-03-18',
            'reason' => "SL — flu-like symptoms. Will submit med cert if needed.",
            'status' => 'approved',
            'approved_by' => $hrId,
            'rejection_reason' => null,
            'created_at' => $now->copy()->subDays(35),
            'updated_at' => $now->copy()->subDays(34),
        ];

        $rows[] = [
            'user_id' => $employees['carlo.pahinga']->id ?? $targetIds[0],
            'leave_type_id' => $leaveTypes['Emergency Leave'] ?? 5,
            'leave_type' => 'Emergency Leave',
            'start_date' => '2026-03-28',
            'end_date' => '2026-03-28',
            'reason' => "Emergency EL — biglang kailangan sa bahay (may aayusin na tubig/kuryente).",
            'status' => 'pending',
            'approved_by' => null,
            'rejection_reason' => null,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ];

        $rows[] = [
            'user_id' => $employees['john.doe']->id ?? $targetIds[0],
            'leave_type_id' => $leaveTypes['Bereavement Leave'] ?? 6,
            'leave_type' => 'Bereavement Leave',
            'start_date' => '2026-03-12',
            'end_date' => '2026-03-13',
            'reason' => "BL — family matter. Requesting 2 days off.",
            'status' => 'rejected',
            'approved_by' => $hrId,
            'rejection_reason' => "Need supporting document / clarification on relationship. Please resubmit with details.",
            'created_at' => $now->copy()->subDays(55),
            'updated_at' => $now->copy()->subDays(54),
        ];

        // A couple more assorted requests for variety
        $rows[] = [
            'user_id' => $employees['angela.fernandez']->id ?? $targetIds[0],
            'leave_type_id' => $leaveTypes['Vacation Leave'] ?? 1,
            'leave_type' => 'Vacation Leave',
            'start_date' => '2026-04-06',
            'end_date' => '2026-04-07',
            'reason' => "VL — personal errands + renewal ng IDs. Will be back Wednesday.",
            'status' => 'pending',
            'approved_by' => null,
            'rejection_reason' => null,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ];

        // Insert only columns that exist (rejection_reason added via later migration)
        $hasRejection = \Illuminate\Support\Facades\Schema::hasColumn('leaves', 'rejection_reason');
        if (!$hasRejection) {
            foreach ($rows as &$r) {
                unset($r['rejection_reason']);
            }
        }

        DB::table('leaves')->insert($rows);
        $this->command?->info('✅ EmployeeLeavesSeeder: seeded ' . count($rows) . ' leave requests.');
    }
}

