<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\User;
use App\Models\AttendanceLog;
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
            $attendanceLog = AttendanceLog::where('user_id', $employee->id)
                ->whereDate('date', $date)
                ->first();
            
            $hoursWorked = 0;
            if ($attendanceLog) {
                $hoursWorked = $attendanceLog->hours_worked ?? 0;
            }
            
            $holidayWage = 0;
            $computationType = 'not_worked';
            
            // Check if previous day was worked (required for unworked regular holiday pay)
            $prevDayWorked = $this->wasPreviousDayWorked($employee->id, $date);
            
            if ($holiday->type === 'regular') {
                $holidayWage = $this->computeRegularHolidayWage(
                    $dailyRate,
                    $hourlyRate,
                    $hoursWorked,
                    $isRestDay,
                    $prevDayWorked,
                    $computationType
                );
            } elseif ($holiday->type === 'special') {
                $holidayWage = $this->computeSpecialHolidayWage(
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
        
        return [
            'holiday_pay' => round($holidayPay, 2),
            'breakdown' => $breakdown,
        ];
    }
    
    /**
     * Regular Holiday Wage Computation (Full pay unworked, 200% if worked)
     * 
     * 1. No Work: Daily Wage × 100%
     * 2. Worked (First 8 Hours): (Daily Wage + COLA) × 200%
     * 3. Overtime (> 8 Hours): Hourly Rate × 200% × 130% × Overtime Hours
     * 4. Rest Day + Regular Holiday: (Daily Wage × 200%) + 30% of that 200%
     */
    private function computeRegularHolidayWage(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool $isRestDay,
        bool $prevDayWorked,
        string &$computationType
    ): float {
        $wage = 0;
        $cola = $this->getCOLA($dailyRate); // Cost of Living Allowance
        
        if ($hoursWorked == 0) {
            // Not worked - but only paid if previous day was worked (or on paid leave)
            if ($prevDayWorked) {
                $wage = $dailyRate * 1.0; // 100%
                $computationType = 'not_worked_paid';
            } else {
                $computationType = 'not_worked_unpaid';
                $wage = 0;
            }
        } elseif ($hoursWorked <= 8) {
            // Worked up to 8 hours
            $regularPay = ($dailyRate + $cola) * 2.0; // 200%
            
            if ($isRestDay) {
                // Rest Day + Regular Holiday: +30% bonus
                $wage = $regularPay + ($regularPay * 0.30);
                $computationType = 'rest_day_regular_holiday';
            } else {
                $wage = $regularPay;
                $computationType = 'worked_8hrs';
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
                $computationType = 'rest_day_regular_holiday_ot';
            } else {
                $wage = $regularPay + $overtimePay;
                $computationType = 'worked_ot';
            }
        }
        
        return $wage;
    }
    
    /**
     * Special Non-Working Day Wage Computation
     * 
     * 1. No Work: "No work, no pay" (₱0)
     * 2. Worked (First 8 Hours): (Daily Wage × 130%) + COLA
     * 3. Overtime (> 8 Hours): Hourly Rate × 130% × 130% × Overtime Hours
     * 4. Rest Day + Special Day: (Daily Wage × 150%) + COLA
     */
    private function computeSpecialHolidayWage(
        float $dailyRate,
        float $hourlyRate,
        float $hoursWorked,
        bool $isRestDay,
        string &$computationType
    ): float {
        $wage = 0;
        $cola = $this->getCOLA($dailyRate);
        
        if ($hoursWorked == 0) {
            // No work = No pay
            $computationType = 'not_worked_no_pay';
            $wage = 0;
        } elseif ($hoursWorked <= 8) {
            // Worked up to 8 hours
            $specialPay = ($dailyRate * 1.30) + $cola; // 130% + COLA
            
            if ($isRestDay) {
                // Rest Day + Special Day: 150% of daily wage + COLA
                $wage = ($dailyRate * 1.50) + $cola;
                $computationType = 'rest_day_special_day';
            } else {
                $wage = $specialPay;
                $computationType = 'worked_8hrs';
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
                $computationType = 'rest_day_special_day_ot';
            } else {
                $wage = $regularPay + $overtimePay;
                $computationType = 'worked_ot';
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
     * Check if previous workday was worked
     * Required for unworked regular holiday pay eligibility
     */
    private function wasPreviousDayWorked(int $userId, Carbon $date): bool
    {
        // Go back to find the last working day (excluding weekends)
        $prevDate = $date->copy()->subDay();
        
        while ($prevDate->isSunday() || $prevDate->isSaturday()) {
            $prevDate->subDay();
        }
        
        $attendanceLog = AttendanceLog::where('user_id', $userId)
            ->whereDate('date', $prevDate)
            ->first();
        
        // Also check if employee was on paid leave
        $paidLeave = $this->wasPaidLeave($userId, $prevDate);
        
        return ($attendanceLog && $attendanceLog->hours_worked > 0) || $paidLeave;
    }
    
    /**
     * Check if date was a paid leave
     */
    private function wasPaidLeave(int $userId, Carbon $date): bool
    {
        // TODO: Implement when Leave model is integrated
        // For now, return false
        return false;
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
