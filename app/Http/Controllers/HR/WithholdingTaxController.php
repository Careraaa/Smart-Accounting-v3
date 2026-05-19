<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\WithholdingTax;

class WithholdingTaxController extends Controller
{
    /**
     * Display a listing of withholding taxes (redirect to statutory deductions).
     */
    public function index()
    {
        return redirect()->route('payroll.statutory-deductions.index');
    }
}
