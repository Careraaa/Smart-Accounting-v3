<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollApprovalController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->where('status', 'pending')->get();
        return view('accountant.payroll-approval.index', compact('payrolls'));
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee', 'deductions', 'allowances')->findOrFail($id);
        return view('accountant.payroll-approval.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        if ($payroll->status != 'pending') {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'approved';
        $payroll->save();

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved.');
    }

    public function reject(Payroll $payroll)
    {
        if ($payroll->status != 'pending') {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'rejected';
        $payroll->save();

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
