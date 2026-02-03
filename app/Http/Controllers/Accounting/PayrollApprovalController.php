<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollApprovalController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::where('status', 'submitted')->with('employee')->get();
        return view('accounting.payroll-approval.index', compact('payrolls'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'deductions');
        return view('accounting.payroll-approval.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved successfully.');
    }

    public function reject(Request $request, Payroll $payroll)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $payroll->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
