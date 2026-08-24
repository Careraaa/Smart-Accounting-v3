<?php

namespace Database\Seeders;

use App\Models\EmployeeAttachment;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EmployeeAttachmentsSeeder extends Seeder
{
    public function run(): void
    {
        $targets = DB::table('users')
            ->whereIn('id', SeedConfig::employeeIds())
            ->select('id', 'username', 'first_name', 'last_name')
            ->get();

        if ($targets->isEmpty()) {
            $this->command?->warn('No employees found; skipping EmployeeAttachmentsSeeder.');
            return;
        }

        $hrId = DB::table('users')->where('role', 'hr')->orderBy('id')->value('id');
        $baseDir = 'seed-attachments';
        Storage::disk('public')->makeDirectory($baseDir);

        DB::table('employee_attachments')->whereIn('user_id', $targets->pluck('id'))->delete();

        $types = EmployeeAttachment::attachmentTypes();
        $statusCycle = ['approved', 'pending', 'approved', 'rejected'];
        $seedKeys = ['drivers_license', 'valid_id_1', 'barangay_clearance', 'medical_cert'];

        $rows = [];
        $now = now();

        foreach ($targets as $u) {
            $full = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->username;
            $slug = strtolower(str_replace([' ', '.'], ['_', '_'], $u->username));

            foreach ($seedKeys as $ki => $key) {
                $status = $statusCycle[($u->id + $ki) % count($statusCycle)];
                $label = $types[$key] ?? $key;
                $filePath = "{$baseDir}/{$slug}_{$key}.svg";
                $this->writePlaceholderSvg($filePath, $full, $label);

                $rows[] = [
                    'user_id' => $u->id,
                    'attachment_key' => $key,
                    'label' => $label,
                    'file_path' => $filePath,
                    'original_name' => strtoupper($key) . '.svg',
                    'mime_type' => 'image/svg+xml',
                    'file_size' => Storage::disk('public')->size($filePath),
                    'status' => $status,
                    'rejection_reason' => $status === 'rejected' ? 'Document is blurry. Please upload a clearer scan.' : null,
                    'reviewed_by' => in_array($status, ['approved', 'rejected'], true) ? $hrId : null,
                    'reviewed_at' => in_array($status, ['approved', 'rejected'], true) ? $now->copy()->subDays(8) : null,
                    'uploaded_by_role' => 'employee',
                    'created_at' => $now->copy()->subDays(14),
                    'updated_at' => $now->copy()->subDays(8),
                ];
            }
        }

        foreach ($rows as &$r) {
            if (!Schema::hasColumn('employee_attachments', 'rejection_reason')) {
                unset($r['rejection_reason']);
            }
            if (!Schema::hasColumn('employee_attachments', 'reviewed_by')) {
                unset($r['reviewed_by']);
            }
            if (!Schema::hasColumn('employee_attachments', 'reviewed_at')) {
                unset($r['reviewed_at']);
            }
            if (!Schema::hasColumn('employee_attachments', 'uploaded_by_role')) {
                unset($r['uploaded_by_role']);
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('employee_attachments')->insert($chunk);
        }

        $this->command?->info('EmployeeAttachmentsSeeder: ' . count($rows) . ' attachments for ' . $targets->count() . ' employees.');
    }

    private function writePlaceholderSvg(string $path, string $fullName, string $label): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        $initial = strtoupper(mb_substr($fullName, 0, 1));
        $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="400" viewBox="0 0 640 400">
  <rect width="640" height="400" fill="#f3f4f6"/>
  <rect x="36" y="36" width="568" height="328" rx="16" fill="#fff" stroke="#d1d5db"/>
  <text x="320" y="180" text-anchor="middle" font-family="sans-serif" font-size="22" fill="#111827">{$safeName}</text>
  <text x="320" y="220" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#6b7280">{$safeLabel}</text>
  <text x="320" y="260" text-anchor="middle" font-family="sans-serif" font-size="48" fill="#9ca3af">{$initial}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);
    }
}
