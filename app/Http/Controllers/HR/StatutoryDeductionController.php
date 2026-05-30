<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\WithholdingTax;
use App\Traits\LogsUserActivity;
use Illuminate\Support\Facades\DB;

class StatutoryDeductionController extends Controller
{
    use LogsUserActivity;
    /**
     * Display a listing of the statutory deductions (read-only reference).
     */
    public function index()
    {
        $deductions = DB::table('statutory_deductions')->orderBy('name')->get();
        $taxes = WithholdingTax::orderBy('name')->get();
        $this->logActivity('viewed', 'Statutory deductions', request()->url(), 'statutory_deduction');
        return view('hr.payroll.statutory-deductions.index', compact('deductions', 'taxes'));
    }
}
