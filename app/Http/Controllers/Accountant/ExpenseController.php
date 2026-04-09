<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseAllocation;
use App\Models\CostCenter;
use App\Models\GLAccount;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display list of expense categories
     */
    public function indexCategories()
    {
        $categories = ExpenseCategory::with('glAccount')->paginate(15);
        return view('accountant.expenses.categories.index', compact('categories'));
    }

    /**
     * Show form to create new expense category
     */
    public function createCategory()
    {
        $glAccounts = GLAccount::where('account_type', 'Expense')->active()->get();
        return view('accountant.expenses.categories.create', compact('glAccounts'));
    }

    /**
     * Store new expense category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name',
            'category_code' => 'required|string|max:50|unique:expense_categories,category_code',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'monthly_budget' => 'nullable|numeric|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_active'] = $request->has('is_active') ? true : false;

        ExpenseCategory::create($validated);

        return redirect()->route('accountant.expenses.categories.index')
            ->with('success', 'Expense category created successfully');
    }

    /**
     * Show expense category details
     */
    public function showCategory(ExpenseCategory $category)
    {
        $allocations = $category->expenses()
            ->with(['costCenter'])
            ->paginate(15);

        return view('accountant.expenses.categories.show', compact('category', 'allocations'));
    }

    /**
     * Show form to edit expense category
     */
    public function editCategory(ExpenseCategory $category)
    {
        $glAccounts = GLAccount::where('account_type', 'Expense')->active()->get();
        return view('accountant.expenses.categories.edit', compact('category', 'glAccounts'));
    }

    /**
     * Update expense category
     */
    public function updateCategory(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name,' . $category->id,
            'category_code' => 'required|string|max:50|unique:expense_categories,category_code,' . $category->id,
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'monthly_budget' => 'nullable|numeric|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $category->update($validated);

        return redirect()->route('accountant.expenses.categories.show', $category)
            ->with('success', 'Expense category updated successfully');
    }

    /**
     * Delete expense category
     */
    public function destroyCategory(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with existing expenses');
        }

        $category->delete();

        return redirect()->route('accountant.expenses.categories.index')
            ->with('success', 'Expense category deleted successfully');
    }

    /**
     * Display expense allocations
     */
    public function indexAllocations()
    {
        $allocations = ExpenseAllocation::with(['journalEntryLine', 'costCenter'])
            ->paginate(15);

        return view('accountant.expenses.allocations.index', compact('allocations'));
    }

    /**
     * Show form to create allocation
     */
    public function createAllocation()
    {
        $costCenters = CostCenter::active()->get();
        return view('accountant.expenses.allocations.create', compact('costCenters'));
    }

    /**
     * Store expense allocation
     */
    public function storeAllocation(Request $request)
    {
        $validated = $request->validate([
            'journal_entry_line_id' => 'required|exists:journal_entry_lines,id',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'allocated_amount' => 'required|numeric|min:0.01',
            'allocation_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        ExpenseAllocation::create($validated);

        return redirect()->route('accountant.expenses.allocations.index')
            ->with('success', 'Expense allocation created successfully');
    }

    /**
     * Show allocation details
     */
    public function showAllocation(ExpenseAllocation $allocation)
    {
        return view('accountant.expenses.allocations.show', compact('allocation'));
    }

    /**
     * Show form to edit allocation
     */
    public function editAllocation(ExpenseAllocation $allocation)
    {
        $costCenters = CostCenter::active()->get();
        return view('accountant.expenses.allocations.edit', compact('allocation', 'costCenters'));
    }

    /**
     * Update allocation
     */
    public function updateAllocation(Request $request, ExpenseAllocation $allocation)
    {
        $validated = $request->validate([
            'cost_center_id' => 'required|exists:cost_centers,id',
            'allocated_amount' => 'required|numeric|min:0.01',
            'allocation_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $allocation->update($validated);

        return redirect()->route('accountant.expenses.allocations.show', $allocation)
            ->with('success', 'Expense allocation updated successfully');
    }

    /**
     * Delete allocation
     */
    public function destroyAllocation(ExpenseAllocation $allocation)
    {
        $allocation->delete();

        return redirect()->route('accountant.expenses.allocations.index')
            ->with('success', 'Expense allocation deleted successfully');
    }

    /**
     * Show expense management dashboard
     */
    public function dashboard()
    {
        $totalExpenseCategories = ExpenseCategory::count();
        $activeCategories = ExpenseCategory::active()->count();
        $totalAllocations = ExpenseAllocation::count();
        
        $topCategories = ExpenseCategory::with('glAccount')
            ->active()
            ->limit(10)
            ->get();

        return view('accountant.expenses.dashboard', compact(
            'totalExpenseCategories',
            'activeCategories',
            'totalAllocations',
            'topCategories'
        ));
    }
}
