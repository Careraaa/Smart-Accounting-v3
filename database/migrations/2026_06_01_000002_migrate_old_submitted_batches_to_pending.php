<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Old batches created as 'submitted' without being finalized should be 'pending'
        DB::table('payroll_batches')
            ->where('status', 'submitted')
            ->whereNull('finalized_at')
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Revert pending batches that never had finalized_at back to submitted
        DB::table('payroll_batches')
            ->where('status', 'pending')
            ->whereNull('finalized_at')
            ->update(['status' => 'submitted']);
    }
};
