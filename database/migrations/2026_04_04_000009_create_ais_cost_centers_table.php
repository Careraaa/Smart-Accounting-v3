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
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('cost_center_code', 50)->unique();
            $table->string('cost_center_name', 255);
            $table->enum('cost_center_type', ['Route', 'Department', 'Vehicle', 'Project', 'Other']);
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('monthly_budget')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('cost_center_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};
