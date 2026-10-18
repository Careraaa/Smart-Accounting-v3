<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boundary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_remittance_id')->constrained('daily_remittances')->restrictOnDelete();
            $table->string('source_payment_key', 64)->unique();
            $table->foreignId('initial_payment_remittance_id')
                ->nullable()
                ->unique()
                ->constrained('daily_remittances')
                ->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('paid_by', 16)->nullable();
            $table->string('payment_kind', 16);
            $table->string('status', 16)->default('Posted');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->unique()->constrained('journal_entries')->restrictOnDelete();
            $table->timestamps();

            $table->index(['daily_remittance_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boundary_payments');
    }
};
