<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class HolidayWageService
{
    /**
     * Calculate holiday wages for an employee during a payroll period.
     * 
     * Returns:
     * - holiday_pay: Additional pay for holidays
     * - holiday_breakdown: Detailed breakdown of holiday calculations
     */
    public function calculateHolidayWages(User $employee, Carbon $start, Carbon $end): array
    {
        $dailyRate = (float) ($employee->salary_rate ?? 0);
        $hourlyRate = $dailyRate / 8;
        
        $holidays = Holiday::whereBetween('date', [$start, $end])->get();
        
        $holidayPay = 0;
        $breakdown = [];

        foreach ($holidays as $holiday) {
            $date = Carbon::parse($holiday->date);
            $isRestDay = $date->isSunday(); // Sunday is rest day
            
            // Get hours worked on this holiday
            $attendance = Attendance::where('user_id', $employee->id)
                ->whereDate('date', $date)
                ->first();
            
            $hoursWorked = 0;
            if ($attendance) {
                $hoursWorked = $attendance->hours_worked ?? 0;
            }
            
            $holidayWage = 0;
            $computationType = 'not_worked';
            
            // Only compute holiday pay if employee actually worked on the holiday
            if ($hoursWorked > 0) {
                if ($holiday->type === 'regular') {
                    $holidayWage = $this->computeRegularHolidayWageWorked(
                        $dailyRate,
                        $hourlyRate,
                        $hoursWorked,
                        $isRestDay,
                        $computationType
                    );
                } elseif ($holiday->type === 'special') {
                    $holidayWage = $this->computeSpecialHolidayWageWorked(
                        $dailyRate,
                        $hourlyRate,
                        $hoursWorked,
                        $isRestDay,
                        $computationType
                    );
                }
                
                $holidayPay += $holidayWage;
                
                $breakdown[] = [
                    'holiday' => $holiday->name,
                    'date' => $holiday->date,
                    'type' => $holiday->type,
                    'is_rest_day' => $isRestDay,
                    'hours_worked' => $hoursWorked,
                    'computation_type' => $computationType,
                    'amount' => round($holidayWage, 2),
                ];
            }
        }
        
        return [
            'holiday_pay' => round($holidayPay, 2),
            'breakdown' => $breakdown,
        ];
    }
    
    /**
     * Regular Holiday Wage Computation (Employee worked on holiday)
     * 
     * 1. Worked (First 8 Hours): (Daily Wage + COLA) × 200%
     * 2. Overtime (> 8 Hours): Hourly Rate × 200% × 130% × Overtime Hours
     * 3. Rest Day + Regular Holiday: (Daily Wage × 200%) + 30% of that 200%
     */
    private function computeRegularHolidayWageWorked(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool $isRestDay,
        string &$computationType
    ): float {
        $wage = 0;
        $cola = $this->getCOLA($dailyRate);
        
        if ($hoursWorked <= 8) {
            // Worked up to 8 hours
            $regularPay = ($dailyRate + $cola) * 2.0; // 200%
            
            if ($isRestDay) {
                // Rest Day + Regular Holiday: +30% bonus
                $wage = $regularPay + ($regularPay * 0.30);
                $computationType = 'worked_rest_day_holiday';
            } else {
                $wage = $regularPay;
                $computationType = 'worked_regular_holiday';
            }
        } else {
            // Worked more than 8 hours (overtime)
            $regularPay = ($dailyRate + $cola) * 2.0; // 200% for first 8
            $overtimeHours = $hoursWorked - 8;
            $overtimePay = $hourlyRate * 2.0 * 1.30 * $overtimeHours; // 200% × 130%
            
            if ($isRestDay) {
                // Rest Day + Regular Holiday with OT: +30% bonus on base
                $basePay = $regularPay + ($regularPay * 0.30);
                $wage = $basePay + $overtimePay;
                $computationType = 'worked_rest_day_holiday_ot';
            } else {
                $wage = $regularPay + $overtimePay;
                $computationType = 'worked_regular_holiday_ot';
            }
        }
        
        return $wage;
    }
    
    /**
     * Special Non-Working Day Wage Computation (Employee worked on special holiday)
     * 
     * 1. Worked (First 8 Hours): (Daily Wage × 130%) + COLA
     * 2. Overtime (> 8 Hours): Hourly Rate × 130% × 130% × Overtime Hours
     * 3. Rest Day + Special Day: (Daily Wage × 150%) + COLA
     */
    private function computeSpecialHolidayWageWorked(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool $isRestDay,
        string &$computationType
    ): float {
        $wage = 0;
        $cola = $this->getCOLA($dailyRate);
        
        if ($hoursWorked <= 8) {
            // Worked up to 8 hours
            $specialPay = ($dailyRate * 1.30) + $cola; // 130% + COLA
            
            if ($isRestDay) {
                // Rest Day + Special Day: 150% of daily wage + COLA
                $wage = ($dailyRate * 1.50) + $cola;
                $computationType = 'worked_rest_day_special';
            } else {
                $wage = $specialPay;
                $computationType = 'worked_special_holiday';
            }
        } else {
            // Worked more than 8 hours
            $regularPay = ($dailyRate * 1.30) + $cola; // 130% + COLA for first 8
            $overtimeHours = $hoursWorked - 8;
            $overtimePay = $hourlyRate * 1.30 * 1.30 * $overtimeHours; // 130% × 130%
            
            if ($isRestDay) {
                // Rest Day + Special Day with OT
                $basePay = ($dailyRate * 1.50) + $cola;
                $wage = $basePay + $overtimePay;
                $computationType = 'worked_rest_day_special_ot';
            } else {
                $wage = $regularPay + $overtimePay;
                $computationType = 'worked_special_holiday_ot';
            }
        }
        
        return $wage;
    }
    
    /**
     * Double Holiday Computation (Two holidays on one day)
     * Worked: 300% of the daily wage
     */
    public function computeDoubleHolidayWage(float $dailyRate, float $hoursWorked): float
    {
        if ($hoursWorked == 0) {
            return 0;
        }
        
        return $dailyRate * 3.0; // 300% of daily wage
    }
    
    /**
     * Special Working Day Computation
     * Employees receive 100% of their daily wage + 30% (if premium applies)
     */
    public function computeSpecialWorkingDayWage(float $dailyRate, bool $isPremium = false): float
    {
        $wage = $dailyRate;
        
        if ($isPremium) {
            $wage += $dailyRate * 0.30; // +30%
        }
        
        return $wage;
    }
    
    /**
     * Get COLA (Cost of Living Allowance)
     * Returns the COLA amount if it's part of the employee's compensation
     * For now, returns 0 (can be enhanced with a setting or employee-specific COLA)
     */
    private function getCOLA(float $dailyRate): float
    {
        // TODO: Configure COLA or fetch from employee/setting
        return 0;
    }
}
