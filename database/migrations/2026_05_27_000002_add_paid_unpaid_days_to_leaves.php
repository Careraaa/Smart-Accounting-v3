<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Track how many days are paid vs unpaid (LWOP)
            $table->integer('paid_days')->default(0)->comment('Days covered by leave credits');
            $table->integer('unpaid_days')->default(0)->comment('Days without leave credits (LWOP)');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['paid_days', 'unpaid_days']);
        });
    }
};
