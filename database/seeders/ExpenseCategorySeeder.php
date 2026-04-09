<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\GLAccount;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fuel', 'description' => 'Diesel and fuel costs', 'gl_account_code' => '5310'],
            ['name' => 'Tire Maintenance', 'description' => 'Tire replacement and repairs', 'gl_account_code' => '5410'],
            ['name' => 'Engine Repair', 'description' => 'Engine and transmission repairs', 'gl_account_code' => '5420'],
            ['name' => 'Body Maintenance', 'description' => 'Vehicle body and paint work', 'gl_account_code' => '5430'],
            ['name' => 'AC Service', 'description' => 'Air conditioning service', 'gl_account_code' => '5440'],
            ['name' => 'Brake Service', 'description' => 'Brake and suspension repairs', 'gl_account_code' => '5450'],
            ['name' => 'Rent & Utilities', 'description' => 'Office rent and utilities', 'gl_account_code' => '5610'],
            ['name' => 'Office Supplies', 'description' => 'Office supplies and materials', 'gl_account_code' => '5620'],
            ['name' => 'Insurance', 'description' => 'Vehicle and property insurance', 'gl_account_code' => '5650'],
            ['name' => 'Professional Fees', 'description' => 'Accounting, legal, and consulting fees', 'gl_account_code' => '5640'],
            ['name' => 'Travel', 'description' => 'Employee travel and transportation', 'gl_account_code' => '5710'],
            ['name' => 'Training', 'description' => 'Employee training and development', 'gl_account_code' => '5720'],
            ['name' => 'Marketing', 'description' => 'Marketing and advertising expenses', 'gl_account_code' => '5730'],
            ['name' => 'Utilities', 'description' => 'Telephone, internet, and communications', 'gl_account_code' => '5630'],
        ];

        foreach ($categories as $category) {
            $glAccount = GLAccount::where('account_code', $category['gl_account_code'])->first();
            
            if ($glAccount) {
                // Generate category code from name (first 3 chars uppercase)
                $categoryCode = strtoupper(substr(str_replace(' ', '', $category['name']), 0, 4)) . '-' . $glAccount->account_code;
                
                ExpenseCategory::firstOrCreate(
                    ['category_name' => $category['name']],
                    [
                        'category_code' => $categoryCode,
                        'description' => $category['description'],
                        'gl_account_id' => $glAccount->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
