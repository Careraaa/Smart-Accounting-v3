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
        Schema::create('cash_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 50)->unique();
            $table->date('receipt_date');
            $table->decimal('amount', 19, 2);
            $table->enum('payment_method', ['Cash', 'Check', 'PDC', 'Bank Transfer', 'Credit Card', 'Other']);
            $table->string('payment_reference')->nullable();
            $table->enum('source_type', ['Daily Remittance', 'Customer Invoice', 'Other']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_reference')->nullable(); // e.g., Route, Driver, Remittance ID
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->boolean('is_deposited')->default(false);
            $table->date('deposit_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('posted_journal_entry_id')->nullable();
            $table->timestamps();
            
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('posted_journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->index('receipt_date');
            $table->index('is_deposited');
            $table->index('source_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_receipts');
    }
};
