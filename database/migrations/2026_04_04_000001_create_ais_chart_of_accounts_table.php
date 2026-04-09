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
        Schema::create('gl_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 10)->unique();
            $table->string('account_name', 255);
            $table->enum('account_type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->enum('account_category', [
                // Asset Categories
                'Cash', 'Bank', 'Accounts Receivable', 'Inventory',
                // Liability Categories
                'Accounts Payable', 'Accrued Payroll', 'Statutory Payable', 'Short-term Loan',
                // Equity Categories
                'Capital', 'Earnings',
                // Revenue Categories
                'Transportation Revenue', 'Other Income',
                // Expense Categories
                'Salary Expense', 'Allowance Expense', 'Vehicle Expense', 'Utilities', 'Depreciation', 'Other Expense'
            ]);
            $table->boolean('is_header')->default(false); // For summary accounts
            $table->decimal('opening_balance', 19, 2)->default(0);
            $table->decimal('current_balance', 19, 2)->default(0);
            $table->boolean('is_draft')->default(false);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_account_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_account_id')->references('id')->on('gl_accounts')->onDelete('set null');
            $table->index('account_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_accounts');
    }
};
