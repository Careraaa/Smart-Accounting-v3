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
        Schema::create('supplier_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number', 50)->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('bill_amount', 19, 2);
            $table->decimal('amount_paid', 19, 2)->default(0);
            $table->decimal('amount_remaining', 19, 2);
            $table->enum('status', ['Draft', 'Received', 'Approved', 'Partially Paid', 'Paid', 'Cancelled']);
            $table->enum('payment_status', ['Pending', 'Partially Paid', 'Paid', 'Overdue']);
            $table->text('bill_description')->nullable();
            $table->string('reference_number')->nullable(); // Supplier's reference
            $table->unsignedBigInteger('posted_journal_entry_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('posted_journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index('bill_date');
            $table->index('due_date');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_bills');
    }
};
