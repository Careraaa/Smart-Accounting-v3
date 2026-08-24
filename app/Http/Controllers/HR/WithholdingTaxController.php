<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\WithholdingTax;
use App\Traits\LogsUserActivity;

class WithholdingTaxController extends Controller
{
    use LogsUserActivity;
    /**
     * Display a listing of withholding taxes (redirect to statutory deductions).
     */
    public function index()
    {
        $this->logActivity('viewed', 'Withholding tax', request()->url(), 'withholding_tax');
        return redirect()->route('payroll.statutory-deductions.index');
    }
}
