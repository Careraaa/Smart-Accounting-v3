<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boundary_payments', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('boundary_payments')
            ->whereNull('approved_by')
            ->update(['approved_by' => DB::raw('recorded_by')]);

        Schema::table('boundary_payments', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable(false)->change();
        });
    }
};
