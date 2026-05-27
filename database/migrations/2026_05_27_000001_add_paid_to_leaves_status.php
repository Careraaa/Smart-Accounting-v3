<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'paid' status to leaves status column
        // Convert from string to enum with explicit values: pending, approved, rejected, paid
        
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `leaves` MODIFY `status` ENUM('pending', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `leaves` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
        }
    }
};
