<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statutory_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "SSS" or "Pag-IBIG"
            
            $table->decimal('min_salary', 12, 2)->default(0);
            $table->decimal('max_salary', 12, 2)->default(0);
            
            $table->decimal('employee_share', 12, 2)->nullable(); // Fixed amount
            $table->decimal('employer_share', 12, 2)->nullable(); // Fixed amount
            
            $table->decimal('percentage_employee', 5, 2)->nullable(); // % of salary
            $table->decimal('percentage_employer', 5, 2)->nullable(); // % of salary

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statutory_deductions');
    }
};
