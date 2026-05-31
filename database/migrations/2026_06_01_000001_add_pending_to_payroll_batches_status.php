<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('pending','submitted','approved','rejected','paid') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('payroll_batches')
            ->where('status', 'pending')
            ->update(['status' => 'submitted']);

        DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('submitted','approved','rejected','paid') NOT NULL DEFAULT 'submitted'");
    }
};
