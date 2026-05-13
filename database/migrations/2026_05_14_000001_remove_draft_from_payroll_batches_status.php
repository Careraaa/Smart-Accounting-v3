<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand enum to include both 'draft' and 'pending' so we can safely migrate data.
        DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','pending','submitted','approved','rejected','paid') NOT NULL DEFAULT 'pending'");

        // Step 2: Migrate any lingering 'draft' rows to 'pending'.
        DB::table('payroll_batches')
            ->where('status', 'draft')
            ->update(['status' => 'pending']);

        // Step 3: Remove 'draft' from the enum now that no rows use it.
        DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('pending','submitted','approved','rejected','paid') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Restore 'draft' to the enum (no data migration needed on rollback).
        DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','pending','submitted','approved','rejected','paid') NOT NULL DEFAULT 'pending'");
    }
};
