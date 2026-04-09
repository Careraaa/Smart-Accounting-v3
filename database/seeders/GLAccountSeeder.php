<?php

namespace Database\Seeders;

use App\Models\GLAccount;
use Illuminate\Database\Seeder;

class GLAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Enhanced Chart of Accounts for Smart Accounting System
     * Includes specialized accounts for:
     * - Driver remittances & management
     * - Fuel & vehicle maintenance
     * - Comprehensive payroll & benefits
     * - Bank accounts for different operations
     */
    public function run(): void
    {
        // ========================================
        // ASSET ACCOUNTS (1000-1999)
        // ========================================

        // Cash & Bank Accounts (1010-1099)
        $this->createAccount('1010', 'Cash on Hand', 'Asset', 'Cash', null, 0);
        $this->createAccount('1020', 'Cash in Bank - Checking (Main)', 'Asset', 'Bank', null, 0);
        $this->createAccount('1025', 'Cash in Bank - Checking (Payroll)', 'Asset', 'Bank', null, 0);
        $this->createAccount('1030', 'Cash in Bank - Savings', 'Asset', 'Bank', null, 0);
        $this->createAccount('1035', 'Cash in Bank - Driver Advance Account', 'Asset', 'Bank', null, 0);
        $this->createAccount('1050', 'Short-term Investments', 'Asset', 'Bank', null, 0);

        // Accounts Receivable (1100-1199)
        $this->createAccount('1100', 'Accounts Receivable - Control', 'Asset', 'Accounts Receivable', null, 0);
        $this->createAccount('1110', 'AR - Trip Billings', 'Asset', 'Accounts Receivable', null, 0);
        $this->createAccount('1120', 'AR - Special Services', 'Asset', 'Accounts Receivable', null, 0);
        $this->createAccount('1130', 'AR - Corporate Accounts', 'Asset', 'Accounts Receivable', null, 0);
        $this->createAccount('1140', 'AR - Government Agencies', 'Asset', 'Accounts Receivable', null, 0);
        $this->createAccount('1150', 'Allowance for Doubtful Accounts', 'Asset', 'Accounts Receivable', null, 0);

        // Prepaid & Current Assets (1200-1299)
        $this->createAccount('1200', 'Prepaid Expenses - Control', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1210', 'Prepaid Insurance', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1220', 'Prepaid Maintenance Contracts', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1230', 'Prepaid Utilities', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1240', 'Prepaid Registration & Permits', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1250', 'Office Supplies Inventory', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1260', 'Fuel Inventory', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1270', 'Parts & Materials Inventory', 'Asset', 'Inventory', null, 0);

        // Fixed Assets - Vehicles & Equipment (1300-1499)
        $this->createAccount('1300', 'Vehicles at Cost - Control', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1310', 'Vehicles - Buses', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1315', 'Vehicles - Vans', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1320', 'Vehicles - Utility Vehicles', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1330', 'Office Equipment', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1340', 'Furniture & Fixtures', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1350', 'Communication Equipment', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1360', 'Maintenance Equipment', 'Asset', 'Inventory', null, 0);

        // Accumulated Depreciation (1500-1599)
        $this->createAccount('1500', 'Accumulated Depreciation - Vehicles', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1510', 'Accumulated Depreciation - Equipment', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1520', 'Accumulated Depreciation - Furniture', 'Asset', 'Inventory', null, 0);

        // Intangible Assets & Other (1600-1999)
        $this->createAccount('1710', 'Goodwill', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1720', 'Software Licenses', 'Asset', 'Inventory', null, 0);
        $this->createAccount('1800', 'Other Assets - Control', 'Asset', 'Inventory', null, 0);

        // ========================================
        // LIABILITY ACCOUNTS (2000-2999)
        // ========================================

        // Accounts Payable (2000-2099)
        $this->createAccount('2010', 'AP - Fuel Suppliers', 'Liability', 'Accounts Payable', null, 0);
        $this->createAccount('2020', 'AP - Maintenance & Parts', 'Liability', 'Accounts Payable', null, 0);
        $this->createAccount('2030', 'AP - Office Expenses', 'Liability', 'Accounts Payable', null, 0);
        $this->createAccount('2040', 'AP - Utilities & Services', 'Liability', 'Accounts Payable', null, 0);
        $this->createAccount('2050', 'AP - Insurance Premiums', 'Liability', 'Accounts Payable', null, 0);

        // Accrued Expenses (2100-2199)
        $this->createAccount('2110', 'Accrued Payroll', 'Liability', 'Accrued Payroll', null, 0);
        $this->createAccount('2120', 'Accrued Benefits', 'Liability', 'Accrued Payroll', null, 0);
        $this->createAccount('2130', 'Accrued Maintenance', 'Liability', 'Accrued Payroll', null, 0);
        $this->createAccount('2140', 'Accrued Professional Fees', 'Liability', 'Accrued Payroll', null, 0);

        // *** DRIVER-RELATED LIABILITIES (2200-2299) - KEY SECTION ***
        $this->createAccount('2210', 'Driver Remittances Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2215', 'Driver Remittances - Outstanding', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2220', 'Driver Cash Advances Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2225', 'Driver Short Remittance Balance', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2230', 'Driver Deductions - Maintenance', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2235', 'Driver Deductions - Fuel Overages', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2240', 'Driver Deductions - Violations', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2245', 'Driver Final Settlements Payable', 'Liability', 'Statutory Payable', null, 0);

        // Statutory Deductions (2300-2399)
        $this->createAccount('2310', 'SSS Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2320', 'PhilHealth Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2330', 'Pag-IBIG Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2340', 'Withholding Tax Payable', 'Liability', 'Statutory Payable', null, 0);
        $this->createAccount('2350', 'BIR Quarterly Tax', 'Liability', 'Statutory Payable', null, 0);

        // Short-term Loans (2400-2499)
        $this->createAccount('2410', 'Short-term Loans Payable', 'Liability', 'Short-term Loan', null, 0);
        $this->createAccount('2420', 'Operating Line of Credit', 'Liability', 'Short-term Loan', null, 0);
        $this->createAccount('2430', 'Current Portion - Long-term Debt', 'Liability', 'Short-term Loan', null, 0);

        // Long-term Liabilities (2500-2599)
        $this->createAccount('2510', 'Vehicle Loans - Long-term', 'Liability', 'Short-term Loan', null, 0);
        $this->createAccount('2520', 'Equipment Loans', 'Liability', 'Short-term Loan', null, 0);
        $this->createAccount('2530', 'Lease Obligations', 'Liability', 'Short-term Loan', null, 0);

        // Other Liabilities (2900-2999)
        $this->createAccount('2910', 'Unearned Revenue', 'Liability', 'Accounts Payable', null, 0);
        $this->createAccount('2920', 'Customer Deposits', 'Liability', 'Accounts Payable', null, 0);

        // ========================================
        // EQUITY ACCOUNTS (3000-3999)
        // ========================================

        // Capital & Ownership (3000-3199)
        $this->createAccount('3010', 'Capital Stock', 'Equity', 'Capital', null, 0);
        $this->createAccount('3020', 'Additional Paid-in Capital', 'Equity', 'Capital', null, 0);
        $this->createAccount('3030', 'Treasury Stock', 'Equity', 'Capital', null, 0);

        // Retained Earnings (3200-3399)
        $this->createAccount('3110', 'Retained Earnings Beginning', 'Equity', 'Earnings', null, 0);
        $this->createAccount('3120', 'Current Year Income(Loss)', 'Equity', 'Earnings', null, 0);
        $this->createAccount('3130', 'Prior Year Adjustments', 'Equity', 'Earnings', null, 0);
        $this->createAccount('3200', 'Owner Withdrawals/Dividends', 'Equity', 'Capital', null, 0);

        // ========================================
        // REVENUE ACCOUNTS (4000-4999)
        // ========================================

        // Transportation Revenue (4000-4199)
        $this->createAccount('4010', 'Regular Trip Revenue', 'Revenue', 'Transportation Revenue', null, 0);
        $this->createAccount('4020', 'Charter Trip Revenue', 'Revenue', 'Transportation Revenue', null, 0);
        $this->createAccount('4030', 'Express Service Revenue', 'Revenue', 'Transportation Revenue', null, 0);
        $this->createAccount('4040', 'Freight/Cargo Revenue', 'Revenue', 'Transportation Revenue', null, 0);
        $this->createAccount('4050', 'Overtime Trip Revenue', 'Revenue', 'Transportation Revenue', null, 0);

        // Ancillary & Other Revenue (4200-4399)
        $this->createAccount('4210', 'Vehicle Rental Revenue', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4220', 'Logistics Services Revenue', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4230', 'Maintenance Services', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4240', 'Driver Services', 'Revenue', 'Other Income', null, 0);

        // Other Operating Income (4400-4499)
        $this->createAccount('4310', 'Interest Income', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4320', 'Miscellaneous Income', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4330', 'Gains on Asset Sales', 'Revenue', 'Other Income', null, 0);
        $this->createAccount('4340', 'Refunds & Adjustments', 'Revenue', 'Other Income', null, 0);

        // ========================================
        // EXPENSE ACCOUNTS (5000-5999)
        // ========================================

        // *** PAYROLL & COMPENSATION (5100-5199) ***
        $this->createAccount('5110', 'Driver Salaries', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5115', 'Driver Incentives & Bonuses', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5120', 'Conductor/Crew Salaries', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5125', 'Mechanic Salaries', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5130', 'Administrative Salaries', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5135', 'Management Salaries', 'Expense', 'Salary Expense', null, 0);
        $this->createAccount('5140', 'Overtime Pay', 'Expense', 'Salary Expense', null, 0);

        // *** EMPLOYEE BENEFITS & DEDUCTIONS (5200-5299) - KEY SECTION ***
        $this->createAccount('5210', 'SSS Employer Contribution', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5220', 'PhilHealth Employer Contribution', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5230', 'Pag-IBIG Employer Contribution', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5240', 'Health Insurance Premium', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5250', 'Life Insurance Premium', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5260', 'Uniform & Safety Equipment', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5270', 'Training & Development', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5280', 'Meal Allowance', 'Expense', 'Allowance Expense', null, 0);
        $this->createAccount('5290', 'Transportation Allowance', 'Expense', 'Allowance Expense', null, 0);

        // *** FUEL & LUBRICANTS (5300-5399) - KEY SECTION ***
        $this->createAccount('5310', 'Diesel Fuel', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5315', 'Fuel Card Purchases', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5320', 'Lubricating Oil', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5325', 'Grease & Coolant', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5330', 'Fuel Shortage Adjustments', 'Expense', 'Vehicle Expense', null, 0);

        // *** VEHICLE MAINTENANCE & REPAIR (5400-5499) - KEY SECTION ***
        $this->createAccount('5410', 'Tire & Wheel Service', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5415', 'Tire Replacement', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5420', 'Engine & Transmission Repair', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5425', 'Engine Oil Change Service', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5430', 'Body & Paint Work', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5435', 'Windshield & Glass', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5440', 'Air Conditioning Service', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5445', 'Electrical System Repair', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5450', 'Brake System Repair', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5455', 'Suspension & Steering', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5460', 'Brake Fluid & Service', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5470', 'Preventive Maintenance', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5475', 'General Repairs & Parts', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5480', 'Outsourced Repair Services', 'Expense', 'Vehicle Expense', null, 0);
        $this->createAccount('5485', 'Parts & Materials', 'Expense', 'Vehicle Expense', null, 0);

        // Vehicle Registration & Insurance (5500-5599)
        $this->createAccount('5510', 'Vehicle Registration & LTO', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5520', 'Comprehensive Motor Insurance', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5525', 'Third-party Liability Insurance', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5530', 'Insurance Claims Deductible', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5535', 'Traffic Violation Fines', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5540', 'Emission Testing & Compliance', 'Expense', 'Other Expense', null, 0);

        // Depreciation Expense (5600-5699)
        $this->createAccount('5610', 'Depreciation - Vehicles', 'Expense', 'Depreciation', null, 0);
        $this->createAccount('5620', 'Depreciation - Equipment', 'Expense', 'Depreciation', null, 0);
        $this->createAccount('5630', 'Depreciation - Furniture', 'Expense', 'Depreciation', null, 0);
        $this->createAccount('5640', 'Depreciation - Communications', 'Expense', 'Depreciation', null, 0);

        // Administrative & Office Expenses (5700-5799)
        $this->createAccount('5710', 'Office Rent', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5715', 'Office Utilities - Electricity', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5720', 'Office Utilities - Water', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5725', 'Telephone & Internet', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5730', 'Office Supplies', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5735', 'Postage & Shipping', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5740', 'Office Equipment Maintenance', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5745', 'Software & Licenses', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5750', 'Professional Fees - Accounting', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5755', 'Professional Fees - Legal', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5760', 'Consulting Services', 'Expense', 'Utilities', null, 0);
        $this->createAccount('5770', 'Banking & Finance Charges', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5775', 'Licenses & Permits', 'Expense', 'Other Expense', null, 0);

        // Marketing & Travel (5800-5899)
        $this->createAccount('5810', 'Advertising & Promotions', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5820', 'Promotional Materials', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5830', 'Employee Travel & Transportation', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5840', 'Client Entertainment', 'Expense', 'Other Expense', null, 0);

        // Non-Operating & Other Expenses (5900-5999)
        $this->createAccount('5910', 'Interest Expense', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5920', 'Penalties & Fines', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5930', 'Donations & Charitable', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5940', 'Loss on Asset Disposal', 'Expense', 'Other Expense', null, 0);
        $this->createAccount('5950', 'Miscellaneous Expense', 'Expense', 'Other Expense', null, 0);
    }

    /**
     * Helper method to create GL accounts
     * Uses firstOrCreate to avoid duplicates
     */
    private function createAccount($code, $name, $type, $category, $parent_id, $opening_balance)
    {
        GLAccount::firstOrCreate(
            ['account_code' => $code],
            [
                'account_name' => $name,
                'account_type' => $type,
                'account_category' => $category,
                'parent_account_id' => $parent_id,
                'opening_balance' => $opening_balance,
                'current_balance' => $opening_balance,
                'is_active' => true,
            ]
        );
    }
}
