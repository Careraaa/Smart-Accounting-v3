<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\StatutoryDeduction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HrReportController extends Controller
{
    public function printEmployeeReport()
    {
        $employees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->orderBy('last_name')->get();

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

        // Get statutory deductions for employer calculations
        $sssDeduction = StatutoryDeduction::where('name', 'SSS')->first();
        $pagibigDeduction = StatutoryDeduction::where('name', 'Pag-IBIG')->first();
        $philhealthDeduction = StatutoryDeduction::where('name', 'PhilHealth')->first();

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
            // Get employee contribution amounts (from payroll record)
            $empSss = $payroll->sss ?? 0;
            $empPagibig = $payroll->pagibig ?? 0;
            $empPhilhealth = $payroll->philhealth ?? 0;

            // Calculate employer contributions based on employee salary
            $baseSalary = $payroll->basic_salary ?? 0;
            
            // SSS Employer: 10.4% of salary (standard rate)
            $empSssShare = $baseSalary * 0.104;
            
            // Pag-IBIG Employer: 2% of salary (standard rate)
            $empPagibigShare = $baseSalary * 0.02;
            
            // PhilHealth Employer: 2.75% of salary (standard rate)
            $empPhilhealthShare = $baseSalary * 0.0275;

            $contribution = [
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

            $contributions[] = $contribution;

            // Add to summary totals
            $summary['employee_sss'] += $contribution['employee_sss'];
            $summary['employer_sss'] += $contribution['employer_sss'];
            $summary['employee_pagibig'] += $contribution['employee_pagibig'];
            $summary['employer_pagibig'] += $contribution['employer_pagibig'];
            $summary['employee_philhealth'] += $contribution['employee_philhealth'];
            $summary['employer_philhealth'] += $contribution['employer_philhealth'];
        }

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

        // Get statutory deductions for employer calculations
        $sssDeduction = StatutoryDeduction::where('name', 'SSS')->first();
        $pagibigDeduction = StatutoryDeduction::where('name', 'Pag-IBIG')->first();
        $philhealthDeduction = StatutoryDeduction::where('name', 'PhilHealth')->first();

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
            // Get employee contribution amounts (from payroll record)
            $empSss = $payroll->sss ?? 0;
            $empPagibig = $payroll->pagibig ?? 0;
            $empPhilhealth = $payroll->philhealth ?? 0;

            // Calculate employer contributions based on employee salary
            $baseSalary = $payroll->basic_salary ?? 0;
            
            // SSS Employer: 10.4% of salary (standard rate)
            $empSssShare = $baseSalary * 0.104;
            
            // Pag-IBIG Employer: 2% of salary (standard rate)
            $empPagibigShare = $baseSalary * 0.02;
            
            // PhilHealth Employer: 2.75% of salary (standard rate)
            $empPhilhealthShare = $baseSalary * 0.0275;

            $contribution = [
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

            $contributions[] = $contribution;

            // Add to summary totals
            $summary['employee_sss'] += $contribution['employee_sss'];
            $summary['employer_sss'] += $contribution['employer_sss'];
            $summary['employee_pagibig'] += $contribution['employee_pagibig'];
            $summary['employer_pagibig'] += $contribution['employer_pagibig'];
            $summary['employee_philhealth'] += $contribution['employee_philhealth'];
            $summary['employer_philhealth'] += $contribution['employer_philhealth'];
        }

        return view('hr.reports.government-contribution-print', compact(
            'employees',
            'contributions',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }
}