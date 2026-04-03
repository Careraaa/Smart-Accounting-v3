<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use Illuminate\Http\Request;

class HrReportController extends Controller
{
    public function printEmployeeReport()
    {
        $employees = Employee::orderBy('last_name')->get();

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
        $payrolls = Payroll::orderByDesc('payroll_period_start')->get();

        $totalBatches  = $payrolls->count();
        $totalPayroll  = $payrolls->sum('net_pay') / 1_000_000;
        $totalPaid     = $payrolls->where('status', 'released')->count();

        // Group into batches the same way PayrollHistoryController does
        $batchData = $payrolls
            ->groupBy(fn($p) => $p->payroll_period_start->format('Y-m-d') . '_' . $p->payroll_period_end->format('Y-m-d'))
            ->map(fn($group) => [
                'period_start' => $group->first()->payroll_period_start,
                'period_end'   => $group->first()->payroll_period_end,
                'count'        => $group->count(),
                'total_amount' => $group->sum('net_pay'),
            ])
            ->values();

        return view('hr.reports.payroll-history-report', compact(
            'batchData',
            'totalBatches',
            'totalPayroll',
            'totalPaid'
        ));
    }
}