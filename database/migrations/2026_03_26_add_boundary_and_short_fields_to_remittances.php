<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $table->decimal('boundary', 10, 2)->nullable()->after('total_expenses');
            $table->boolean('is_short_remittance')->default(false)->after('net_remittance');
            $table->decimal('short_amount', 10, 2)->nullable()->after('is_short_remittance');
            $table->decimal('driver_share', 10, 2)->nullable()->after('short_amount');
            $table->decimal('pao_share', 10, 2)->nullable()->after('driver_share');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $table->dropColumn(['boundary', 'is_short_remittance', 'short_amount', 'driver_share', 'pao_share']);
        });
    }
};
