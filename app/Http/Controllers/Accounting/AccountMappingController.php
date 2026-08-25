<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountMapping;
use App\Models\ChartOfAccount;
use App\Models\AccountingAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountMappingController extends Controller
{
    public function index()
    {
        $mappings = AccountMapping::with('account')->get();
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();

        $mappingDefinitions = [
            'payroll_expense'         => 'Salaries and Wages Expense',
            'sss_payable'             => 'SSS Payable',
            'philhealth_payable'      => 'PhilHealth Payable',
            'pagibig_payable'         => 'Pag-IBIG Payable',
            'withholding_tax_payable' => 'Withholding Tax Payable',
            'salaries_payable'        => 'Salaries Payable',
            'thirteenth_month_expense' => '13th Month Pay Expense',
            'thirteenth_month_payable' => '13th Month Payable',
        ];

        $existingMappings = [];
        foreach ($mappings as $mapping) {
            $existingMappings[$mapping->mapping_key] = $mapping->account_id;
        }

        return view('accounting.account-mappings.index', compact('accounts', 'mappingDefinitions', 'existingMappings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mappings'            => 'required|array',
            'mappings.*.key'      => 'required|string|max:50',
            'mappings.*.account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['mappings'] as $mapping) {
                AccountMapping::updateOrCreate(
                    ['mapping_key' => $mapping['key']],
                    [
                        'label'     => $mapping['key'],
                        'account_id' => $mapping['account_id'],
                    ]
                );
            }

            AccountingAuditLog::log(
                'updated',
                'Account Mapping',
                null,
                'Updated account mappings',
                null,
                ['mappings' => $validated['mappings']]
            );

            DB::commit();

            return redirect()->route('accounting.account-mappings.index')
                ->with('success', 'Account mappings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update account mappings: ' . $e->getMessage());
        }
    }
}
