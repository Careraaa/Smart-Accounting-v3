<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('year')->nullable()->change();
            $table->string('model')->nullable()->change();
            $table->string('make')->nullable()->change();
            $table->string('vehicle_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('year')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
            $table->string('make')->nullable(false)->change();
            $table->string('vehicle_type')->nullable(false)->change();
        });
    }
};
