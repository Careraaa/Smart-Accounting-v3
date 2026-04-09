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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('je_number', 50)->unique();
            $table->date('je_date');
            $table->date('posting_date')->nullable();
            $table->text('description');
            $table->enum('status', ['Draft', 'Pending Approval', 'Approved', 'Posted', 'Rejected', 'Reversed']);
            $table->enum('je_type', [
                'Manual Entry', 'Automated Payroll', 'Automated Cash Receipt',
                'Automated Expense', 'Automated Depreciation', 'Bank Reconciliation',
                'Period Adjustment', 'Reversal'
            ]);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->string('reference_number')->nullable(); // Link to source (Payroll ID, Remittance ID, etc.)
            $table->string('reference_type')->nullable(); // Type of reference (Payroll, DailyRemittance, etc.)
            $table->decimal('total_debit', 19, 2)->default(0);
            $table->decimal('total_credit', 19, 2)->default(0);
            $table->boolean('is_balanced')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reversed_by_je_id')->nullable(); // Link to reversal entry
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index('je_date');
            $table->index('status');
            $table->index('je_type');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
