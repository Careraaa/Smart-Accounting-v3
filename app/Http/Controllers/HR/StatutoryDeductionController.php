<?php

namespace App\Http\Controllers\HR; // your namespace

use App\Http\Controllers\Controller; // base controller
use Illuminate\Http\Request; // for handling requests
use Illuminate\Support\Facades\DB; // <-- add this line
use App\Models\StatutoryDeduction;

class StatutoryDeductionController extends Controller
{
    /**
     * Display a listing of the statutory deductions.
     */
    public function index()
    {
        $deductions = DB::table('statutory_deductions')->orderBy('name')->get();
        return view('hr.payroll.statutory-deductions.index', compact('deductions'));
    }

    /**
     * Show the form for creating a new statutory deduction.
     */
    public function create()
    {
        return view('hr.payroll.statutory-deductions.create');
    }

    /**
     * Store a newly created statutory deduction in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|gte:min_salary',
            'employee_share' => 'nullable|numeric|min:0',
            'employer_share' => 'nullable|numeric|min:0',
            'percentage_employee' => 'nullable|numeric|min:0|max:100',
            'percentage_employer' => 'nullable|numeric|min:0|max:100',
        ]);

        // Insert into DB
        DB::table('statutory_deductions')->insert([
            'name' => $request->name,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'employee_share' => $request->employee_share,
            'employer_share' => $request->employer_share,
            'percentage_employee' => $request->percentage_employee,
            'percentage_employer' => $request->percentage_employer,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('statutory-deductions.index')->with('success', 'Statutory deduction added successfully!');
    }

    /**
     * Show the form for editing the specified statutory deduction.
     */
    public function edit($id)
    {
        $deduction = DB::table('statutory_deductions')->where('id', $id)->first();
        if (!$deduction) {
            return redirect()->route('statutory-deductions.index')->with('error', 'Statutory deduction not found.');
        }
        return view('hr.payroll.statutory-deductions.edit', compact('deduction'));
    }

    /**
     * Update the specified statutory deduction in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|gte:min_salary',
            'employee_share' => 'nullable|numeric|min:0',
            'employer_share' => 'nullable|numeric|min:0',
            'percentage_employee' => 'nullable|numeric|min:0|max:100',
            'percentage_employer' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::table('statutory_deductions')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'employee_share' => $request->employee_share,
                'employer_share' => $request->employer_share,
                'percentage_employee' => $request->percentage_employee,
                'percentage_employer' => $request->percentage_employer,
                'updated_at' => now(),
            ]);

        return redirect()->route('statutory-deductions.index')->with('success', 'Statutory deduction updated successfully!');
    }

    /**
     * Remove the specified statutory deduction from storage.
     */
    public function destroy($id)
    {
        DB::table('statutory_deductions')->where('id', $id)->delete();

        return redirect()->route('statutory-deductions.index')->with('success', 'Statutory deduction deleted successfully!');
    }
}
