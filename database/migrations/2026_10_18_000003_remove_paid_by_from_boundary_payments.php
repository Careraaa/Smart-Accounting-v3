<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Preserve the optional partner allocation used for separate shortage shares.
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('boundary_payments', 'paid_by')) {
            Schema::table('boundary_payments', function (Blueprint $table) {
                $table->string('paid_by', 16)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('boundary_payments', 'paid_by')) {
            Schema::table('boundary_payments', function (Blueprint $table) {
                $table->string('paid_by', 16)->default('joint');
            });
        }
    }
};
