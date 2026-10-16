<?php

namespace App\Services;

use App\Models\StatutoryDeduction;
use App\Models\User;

class StatutoryContributionService
{
    public function employerShares(User $employee, float $monthlyEquivalent, bool $hasIncome): array
    {
        if (!$hasIncome) {
            return ['sss' => 0.0, 'pagibig' => 0.0, 'philhealth' => 0.0];
        }

        return [
            'sss' => $employee->has_sss ? $this->employerShare('SSS', $monthlyEquivalent) : 0.0,
            'pagibig' => $employee->has_pagibig ? $this->employerShare('Pag-IBIG', $monthlyEquivalent) : 0.0,
            'philhealth' => $employee->has_philhealth ? $this->employerShare('PhilHealth', $monthlyEquivalent) : 0.0,
        ];
    }

    public function employerShare(string $name, float $monthlyEquivalent): float
    {
        $row = StatutoryDeduction::where('name', $name)
            ->where('min_salary', '<=', $monthlyEquivalent)
            ->where(function ($query) use ($monthlyEquivalent) {
                $query->where('max_salary', '>=', $monthlyEquivalent)
                    ->orWhereNull('max_salary');
            })
            ->orderByDesc('min_salary')
            ->first();

        if (!$row) {
            return 0.0;
        }

        if ($name === 'Pag-IBIG' && (float) $row->percentage_employer > 0) {
            $monthly = min(
                $monthlyEquivalent * ((float) $row->percentage_employer / 100),
                200.0
            );

            return round($monthly / 2, 2);
        }

        if ($name === 'PhilHealth') {
            if ((float) $row->employer_share > 0) {
                return round((float) $row->employer_share / 2, 2);
            }

            if ((float) $row->percentage_employer > 0) {
                $monthly = min(
                    $monthlyEquivalent * ((float) $row->percentage_employer / 100),
                    2500.0
                );

                return round($monthly / 2, 2);
            }
        }

        if (!is_null($row->employer_share)) {
            return round((float) $row->employer_share / 2, 2);
        }

        if ($name === 'Pag-IBIG' && !is_null($row->percentage_employer)) {
            $monthly = min(
                $monthlyEquivalent * ((float) $row->percentage_employer / 100),
                200.0
            );

            return round($monthly / 2, 2);
        }

        return 0.0;
    }
}
