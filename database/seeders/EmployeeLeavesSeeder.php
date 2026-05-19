<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeLeavesSeeder extends Seeder
{
    public function run(): void
    {
        $employeeIds = SeedConfig::employeeIds();
        $hrId = DB::table('users')->where('role', 'hr')->orderBy('id')->value('id');

        $leaveTypes = DB::table('leave_types')
            ->whereIn('name', ['Vacation Leave', 'Sick Leave', 'Emergency Leave', 'Bereavement Leave'])
            ->pluck('id', 'name');

        if ($leaveTypes->isEmpty()) {
            $this->command?->warn('Leave types not found; skipping EmployeeLeavesSeeder.');
            return;
        }

        DB::table('leaves')->whereIn('user_id', $employeeIds)->delete();

        $rows = [];
        $hasRejection = Schema::hasColumn('leaves', 'rejection_reason');

        $templates = [
            ['type' => 'Vacation Leave', 'start' => '2026-02-10', 'end' => '2026-02-12', 'reason' => 'Family visit to province; tasks endorsed to team lead.', 'status' => 'approved'],
            ['type' => 'Sick Leave', 'start' => '2026-02-24', 'end' => '2026-02-24', 'reason' => 'Fever and body pain; rest advised by clinic.', 'status' => 'approved'],
            ['type' => 'Vacation Leave', 'start' => '2026-03-17', 'end' => '2026-03-19', 'reason' => 'Scheduled VL; son\'s school recognition day.', 'status' => 'approved'],
            ['type' => 'Sick Leave', 'start' => '2026-03-24', 'end' => '2026-03-25', 'reason' => 'Dental procedure and recovery.', 'status' => 'approved'],
            ['type' => 'Emergency Leave', 'start' => '2026-04-02', 'end' => '2026-04-02', 'reason' => 'Urgent home repair (flooding after heavy rain).', 'status' => 'approved'],
            ['type' => 'Vacation Leave', 'start' => '2026-04-14', 'end' => '2026-04-15', 'reason' => 'Personal errands and government ID renewal.', 'status' => 'pending'],
            ['type' => 'Bereavement Leave', 'start' => '2026-03-06', 'end' => '2026-03-07', 'reason' => 'Death of close relative; travel to hometown.', 'status' => 'rejected', 'rejection' => 'Please submit death certificate or obituary for HR records.'],
            ['type' => 'Emergency Leave', 'start' => '2026-04-22', 'end' => '2026-04-22', 'reason' => 'Child picked up from school due to illness.', 'status' => 'pending'],
        ];

        foreach (SeedConfig::employeeIds() as $i => $userId) {
            $tpl = $templates[$i % count($templates)];
            $approved = $tpl['status'] === 'approved';

            $row = [
                'user_id' => $userId,
                'leave_type_id' => $leaveTypes[$tpl['type']] ?? $leaveTypes->first(),
                'leave_type' => $tpl['type'],
                'start_date' => $tpl['start'],
                'end_date' => $tpl['end'],
                'reason' => $tpl['reason'],
                'status' => $tpl['status'],
                'approved_by' => $approved ? $hrId : null,
                'rejection_reason' => ($tpl['status'] === 'rejected') ? ($tpl['rejection'] ?? 'Incomplete documentation.') : null,
                'created_at' => $tpl['start'] . ' 09:00:00',
                'updated_at' => $tpl['start'] . ' 10:00:00',
            ];

            if (!$hasRejection) {
                unset($row['rejection_reason']);
            }

            $rows[] = $row;
        }

        DB::table('leaves')->insert($rows);
        $this->command?->info('EmployeeLeavesSeeder: ' . count($rows) . ' leave requests (Feb–Apr 2026).');
    }
}
