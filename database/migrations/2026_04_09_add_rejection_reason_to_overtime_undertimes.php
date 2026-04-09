<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_undertimes', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('overtime_undertimes', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'approved_by']);
        });
    }
};
