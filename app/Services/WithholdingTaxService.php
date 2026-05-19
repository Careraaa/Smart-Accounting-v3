<?php

namespace App\Services;

use App\Models\WithholdingTax;

class WithholdingTaxService
{
    /**
     * Calculate withholding tax based on salary and frequency
     * 
     * @param float $salary The employee's salary
     * @param string $frequency Daily, Weekly, Semi-monthly, or Monthly
     * @return float The withholding tax amount
     */
    public static function calculate(float $salary, string $frequency = 'Monthly'): float
    {
        $tax = WithholdingTax::where('description', $frequency)
            ->where('min_salary', '<=', $salary)
            ->where('max_salary', '>=', $salary)
            ->first();

        if (!$tax) {
            return 0;
        }

        $withholdingTax = $tax->employee_share ?? 0;

        if ($tax->percentage_employee) {
            $percentageAmount = ($salary - $tax->min_salary) * ($tax->percentage_employee / 100);
            $withholdingTax += $percentageAmount;
        }

        return round($withholdingTax, 2);
    }

    /**
     * Get all withholding tax brackets for a specific frequency
     * 
     * @param string $frequency Daily, Weekly, Semi-monthly, or Monthly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBrackets(string $frequency = 'Monthly')
    {
        return WithholdingTax::where('description', $frequency)
            ->orderBy('min_salary')
            ->get();
    }

    /**
     * Get all unique frequencies available
     * 
     * @return array
     */
    public static function getFrequencies(): array
    {
        return WithholdingTax::distinct()
            ->pluck('description')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }
}
