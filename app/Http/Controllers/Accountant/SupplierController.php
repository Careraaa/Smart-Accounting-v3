<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierBill;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display list of suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier_code', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('supplier_name')
            ->paginate(20);

        return view('accountant.suppliers.index', compact('suppliers'));
    }

    /**
     * Show supplier detail
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['bills']);
        $outstandingBalance = $supplier->getOutstandingBalance();
        $totalBills = $supplier->bills()->count();
        $paidBills = $supplier->bills()->where('payment_status', 'Paid')->count();

        return view('accountant.suppliers.show', compact('supplier', 'outstandingBalance', 'totalBills', 'paidBills'));
    }

    /**
     * Create new supplier
     */
    public function create()
    {
        return view('accountant.suppliers.create');
    }

    /**
     * Store supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|string|unique:suppliers|max:50',
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'required|in:COD,Net 7,Net 15,Net 30,Net 60,Custom',
            'payment_terms_description' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        Supplier::create($validated);

        return redirect()->route('accountant.suppliers.index')
            ->with('success', 'Supplier created successfully');
    }

    /**
     * Edit supplier
     */
    public function edit(Supplier $supplier)
    {
        return view('accountant.suppliers.edit', compact('supplier'));
    }

    /**
     * Update supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'required|in:COD,Net 7,Net 15,Net 30,Net 60,Custom',
            'payment_terms_description' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('accountant.suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully');
    }
}
