<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('thirteenth_month_pays')) {
            return;
        }

        Schema::create('thirteenth_month_pays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('calendar_year');
            $table->decimal('total_basic_salary_earned', 14, 2)->default(0);
            $table->decimal('thirteenth_month_pay', 14, 2)->default(0);
            $table->decimal('months_worked', 5, 2)->default(0);
            $table->boolean('is_eligible')->default(true);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('amount_remaining', 14, 2)->default(0);
            $table->string('status')->default('pending'); // pending, partial, paid
            $table->date('payment_date')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('computed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('computed_at')->nullable();
            $table->json('computation_breakdown')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'calendar_year']);
            $table->index(['calendar_year', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thirteenth_month_pays');
    }
};
