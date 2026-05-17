<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // fixed_amount, percentage_based, formula_based, mandatory_bonus
            $table->string('computation_method'); // manual_amount, automatic_formula
            $table->text('formula')->nullable();
            $table->decimal('fixed_amount', 12, 2)->nullable();
            $table->decimal('percentage_value', 8, 4)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_system_generated')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('payroll_period')->nullable();
            $table->string('code')->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bonus_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_id')->constrained('bonuses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['bonus_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_employee');
        Schema::dropIfExists('bonuses');
    }
};
