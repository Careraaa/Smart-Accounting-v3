<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\StatutoryDeduction;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class HrReportController extends Controller
{
    use LogsUserActivity;
    /**
     * Compute employer statutory contribution using the same bracket-based
     * logic as PayrollService, for consistent government contribution reporting.
     * Returns the semi-monthly employer share.
     */
    private function getEmployerContribution(string $name, float $salary): float
    {
        $row = StatutoryDeduction::where('name', $name)
            ->where('min_salary', '<=', $salary)
            ->where(function ($q) use ($salary) {
                $q->where('max_salary', '>=', $salary)
                  ->orWhereNull('max_salary');
            })
            ->orderByDesc('min_salary')
            ->first();

        if (!$row) return 0;

        if (!is_null($row->employer_share)) {
            return round($row->employer_share / 2, 2);
        }

        if (!is_null($row->percentage_employer)) {
            $monthly = $salary * ($row->percentage_employer / 100);
            if ($name === 'Pag-IBIG') $monthly = min($monthly, 200.0);
            if ($name === 'PhilHealth') $monthly = min($monthly, 2500.0);
            return round($monthly / 2, 2);
        }

        return 0;
    }
    public function printEmployeeReport()
    {
        $employees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->orderBy('last_name')->get();

        $this->logActivity('viewed', 'Employee report', request()->url(), 'report');

        return view('hr.reports.employee-report', compact('employees'));
    }

    public function printApprovedLeavesReport()
    {
        $leaves = Leave::with(['employee', 'approvedBy'])
            ->where('status', 'approved')
            ->orderByDesc('updated_at')
            ->get();

        $approvedLeaves  = $leaves->count();
        $thisWeekLeaves  = $leaves->filter(fn($l) => $l->updated_at->isCurrentWeek())->count();
        $thisMonthLeaves = $leaves->filter(fn($l) => $l->updated_at->isCurrentMonth())->count();

        $this->logActivity('viewed', 'Approved leaves report', request()->url(), 'report');

        return view('hr.reports.approved-leaves-report', compact(
            'leaves',
            'approvedLeaves',
            'thisWeekLeaves',
            'thisMonthLeaves'
        ));
    }

    public function printPayrollHistoryReport()
    {
        $payrolls = Payroll::with('user')
            ->orderByDesc('payroll_period_start')
            ->get();

        $totalEmployees = $payrolls->count();
        $totalGrossPay  = $payrolls->sum('gross_pay');
        $totalDeductions = $payrolls->sum('total_deductions');
        $totalNetPay    = $payrolls->sum('net_pay');

        $this->logActivity('viewed', 'Payroll history report', request()->url(), 'report');

        return view('hr.reports.payroll-history-report', compact(
            'payrolls',
            'totalEmployees',
            'totalGrossPay',
            'totalDeductions',
            'totalNetPay'
        ));
    }

    public function governmentContributionReport(Request $request)
    {
        // Get date filters from request - default to last 3 months to capture seeded data
        $dateFrom = $request->get('date_from', Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');

        // Get list of all employees for filter dropdown
        $employees = Employee::where('role', 'employee')
            ->orderBy('first_name')
            ->get();

        // Get payroll records for the period - check both start and end dates
        $payrolls = Payroll::with('user')
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('payroll_period_start', [$dateFrom, $dateTo])
                  ->orWhereBetween('payroll_period_end', [$dateFrom, $dateTo]);
            })
            ->when($employeeId, function ($q) use ($employeeId) {
                return $q->where('user_id', $employeeId);
            })
            ->orderByDesc('payroll_period_start')
            ->get();

        // Build contributions array
        $contributions = [];
        $summary = [
            'employee_sss' => 0,
            'employer_sss' => 0,
            'employee_pagibig' => 0,
            'employer_pagibig' => 0,
            'employee_philhealth' => 0,
            'employer_philhealth' => 0,
        ];

        foreach ($payrolls as $payroll) {
            // Get employee data
            $employee = $payroll->user;
            
            // Get employee contribution amounts (from payroll record)
            $empSss = $payroll->sss ?? 0;
            $empPagibig = $payroll->pagibig ?? 0;
            $empPhilhealth = $payroll->philhealth ?? 0;

            // Use monthly equivalent salary for government bracket lookups.
            //   5-day workweek: dailyRate × 21.75
            //   6-day workweek: dailyRate × 26.08
            $dailyRate = (float) ($employee->salary_rate ?? 0);
            $workDaysPerWeek = (int) ($employee->work_days_per_week ?? 5);
            $monthlyEquivalent = $workDaysPerWeek === 6 ? $dailyRate * 26.08 : $dailyRate * 21.75;
            
            // SSS Employer: Only calculate if employee has enrolled for SSS
            $empSssShare = 0;
            if ($employee->has_sss) {
                $sssRecord = StatutoryDeduction::where('name', 'SSS')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($sssRecord) {
                    $empSssShare = round(($sssRecord->employer_share ?? 0) / 2, 2);
                }
            }
            
            // Pag-IBIG Employer: Only calculate if employee has enrolled for Pag-IBIG
            $empPagibigShare = 0;
            if ($employee->has_pagibig) {
                $pagibigRecord = StatutoryDeduction::where('name', 'Pag-IBIG')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($pagibigRecord) {
                    // Use percentage if available, otherwise use fixed share
                    if ($pagibigRecord->percentage_employer) {
                        $monthlyEr = $monthlyEquivalent * ($pagibigRecord->percentage_employer / 100);
                        $empPagibigShare = round(min($monthlyEr, 200.0) / 2, 2);
                    } else {
                        $empPagibigShare = round(($pagibigRecord->employer_share ?? 0) / 2, 2);
                    }
                }
            }
            
            // PhilHealth Employer: Only calculate if employee has enrolled for PhilHealth
            $empPhilhealthShare = 0;
            if ($employee->has_philhealth) {
                $philhealthRecord = StatutoryDeduction::where('name', 'PhilHealth')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($philhealthRecord) {
                    // Use fixed share if available, otherwise use percentage
                    if ($philhealthRecord->employer_share) {
                        $empPhilhealthShare = round($philhealthRecord->employer_share / 2, 2);
                    } else if ($philhealthRecord->percentage_employer) {
                        $monthlyEr = $monthlyEquivalent * ($philhealthRecord->percentage_employer / 100);
                        $empPhilhealthShare = round(min($monthlyEr, 2500.0) / 2, 2);
                    }
                }
            }

            $contributions[] = [
                'employee_name' => $payroll->user->name ?? 'Unknown',
                'period_start' => $payroll->payroll_period_start,
                'period_end' => $payroll->payroll_period_end,
                'employee_sss' => round($empSss, 2),
                'employer_sss' => round($empSssShare, 2),
                'employee_pagibig' => round($empPagibig, 2),
                'employer_pagibig' => round($empPagibigShare, 2),
                'employee_philhealth' => round($empPhilhealth, 2),
                'employer_philhealth' => round($empPhilhealthShare, 2),
            ];

            $summary['employee_sss'] += $empSss;
            $summary['employer_sss'] += $empSssShare;
            $summary['employee_pagibig'] += $empPagibig;
            $summary['employer_pagibig'] += $empPagibigShare;
            $summary['employee_philhealth'] += $empPhilhealth;
            $summary['employer_philhealth'] += $empPhilhealthShare;
        }

        $this->logActivity('viewed', 'Government contribution report', request()->url(), 'report');

        return view('hr.reports.government-contribution', compact(
            'employees',
            'contributions',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }

    public function governmentContributionPrint(Request $request)
    {
        // Get date filters from request
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');

        // Get list of all employees for filter dropdown
        $employees = Employee::where('role', 'employee')
            ->orderBy('first_name')
            ->get();

        // Get payroll records for the period - check both start and end dates
        $payrolls = Payroll::with('user')
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('payroll_period_start', [$dateFrom, $dateTo])
                  ->orWhereBetween('payroll_period_end', [$dateFrom, $dateTo]);
            })
            ->when($employeeId, function ($q) use ($employeeId) {
                return $q->where('user_id', $employeeId);
            })
            ->orderByDesc('payroll_period_start')
            ->get();

        // Build contributions array
        $contributions = [];
        $summary = [
            'employee_sss' => 0,
            'employer_sss' => 0,
            'employee_pagibig' => 0,
            'employer_pagibig' => 0,
            'employee_philhealth' => 0,
            'employer_philhealth' => 0,
        ];

        foreach ($payrolls as $payroll) {
            // Get employee data
            $employee = $payroll->user;
            
            // Get employee contribution amounts (from payroll record)
            $empSss = $payroll->sss ?? 0;
            $empPagibig = $payroll->pagibig ?? 0;
            $empPhilhealth = $payroll->philhealth ?? 0;

            // Use monthly equivalent salary for government bracket lookups.
            //   5-day workweek: dailyRate × 21.75
            //   6-day workweek: dailyRate × 26.08
            $dailyRate = (float) ($employee->salary_rate ?? 0);
            $workDaysPerWeek = (int) ($employee->work_days_per_week ?? 5);
            $monthlyEquivalent = $workDaysPerWeek === 6 ? $dailyRate * 26.08 : $dailyRate * 21.75;
            
            // SSS Employer: Only calculate if employee has enrolled for SSS
            $empSssShare = 0;
            if ($employee->has_sss) {
                $sssRecord = StatutoryDeduction::where('name', 'SSS')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($sssRecord) {
                    $empSssShare = round(($sssRecord->employer_share ?? 0) / 2, 2);
                }
            }
            
            // Pag-IBIG Employer: Only calculate if employee has enrolled for Pag-IBIG
            $empPagibigShare = 0;
            if ($employee->has_pagibig) {
                $pagibigRecord = StatutoryDeduction::where('name', 'Pag-IBIG')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($pagibigRecord) {
                    // Use percentage if available, otherwise use fixed share
                    if ($pagibigRecord->percentage_employer) {
                        $monthlyEr = $monthlyEquivalent * ($pagibigRecord->percentage_employer / 100);
                        $empPagibigShare = round(min($monthlyEr, 200.0) / 2, 2);
                    } else {
                        $empPagibigShare = round(($pagibigRecord->employer_share ?? 0) / 2, 2);
                    }
                }
            }
            
            // PhilHealth Employer: Only calculate if employee has enrolled for PhilHealth
            $empPhilhealthShare = 0;
            if ($employee->has_philhealth) {
                $philhealthRecord = StatutoryDeduction::where('name', 'PhilHealth')
                    ->where('min_salary', '<=', $monthlyEquivalent)
                    ->where('max_salary', '>=', $monthlyEquivalent)
                    ->first();
                if ($philhealthRecord) {
                    // Use fixed share if available, otherwise use percentage
                    if ($philhealthRecord->employer_share) {
                        $empPhilhealthShare = round($philhealthRecord->employer_share / 2, 2);
                    } else if ($philhealthRecord->percentage_employer) {
                        $monthlyEr = $monthlyEquivalent * ($philhealthRecord->percentage_employer / 100);
                        $empPhilhealthShare = round(min($monthlyEr, 2500.0) / 2, 2);
                    }
                }
            }

            $contributions[] = [
                'employee_name' => $payroll->user->name ?? 'Unknown',
                'period_start' => $payroll->payroll_period_start,
                'period_end' => $payroll->payroll_period_end,
                'employee_sss' => round($empSss, 2),
                'employer_sss' => round($empSssShare, 2),
                'employee_pagibig' => round($empPagibig, 2),
                'employer_pagibig' => round($empPagibigShare, 2),
                'employee_philhealth' => round($empPhilhealth, 2),
                'employer_philhealth' => round($empPhilhealthShare, 2),
            ];

            $summary['employee_sss'] += $empSss;
            $summary['employer_sss'] += $empSssShare;
            $summary['employee_pagibig'] += $empPagibig;
            $summary['employer_pagibig'] += $empPagibigShare;
            $summary['employee_philhealth'] += $empPhilhealth;
            $summary['employer_philhealth'] += $empPhilhealthShare;
        }

        $this->logActivity('viewed', 'Government contribution print report', request()->url(), 'report');

        return view('hr.reports.government-contribution-print', compact(
            'employees',
            'contributions',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }
}