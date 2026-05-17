<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\ThirteenthMonthPay;
use App\Models\User;
use App\Services\BonusFormulaEngine;
use App\Services\ThirteenthMonthPayService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BonusController extends Controller
{
    public function __construct(
        protected BonusFormulaEngine $formulaEngine,
        protected ThirteenthMonthPayService $thirteenthMonthPayService
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = trim((string) $request->get('search', ''));

        $query = Bonus::query()->with('creator');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('formula', 'like', "%{$search}%");
            });
        }

        $bonuses = $query->orderByDesc('is_mandatory')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('hr.bonuses.index', compact('bonuses', 'status', 'search'));
    }

    public function create()
    {
        $employees = $this->employeeOptions();
        $formulaVariables = BonusFormulaEngine::variableHelp();

        return view('hr.bonuses.create', compact('employees', 'formulaVariables'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBonus($request);

        $bonus = Bonus::create(array_merge($validated, [
            'created_by' => auth()->id(),
            'is_mandatory' => $validated['type'] === Bonus::TYPE_MANDATORY,
            'is_system_generated' => false,
        ]));

        $this->syncEligibleEmployees($bonus, $request->input('eligible_employees', []));

        return redirect()
            ->route('bonuses.index')
            ->with('success', 'Bonus created successfully.');
    }

    public function edit(Request $request, Bonus $bonus)
    {
        if (! $bonus->isEditableBy(auth()->user())) {
            abort(403, 'Only administrators can edit mandatory system bonuses.');
        }

        $bonus->load('eligibleEmployees');
        $employees = $this->employeeOptions();
        $formulaVariables = BonusFormulaEngine::variableHelp();

        $thirteenthMonthData = null;

        if ($bonus->isThirteenthMonthPay()) {
            $calendarYear = (int) $request->get('year', $bonus->year ?? now()->year);
            $records = ThirteenthMonthPay::with('user')
                ->where('calendar_year', $calendarYear)
                ->orderBy('thirteenth_month_pay', 'desc')
                ->get();

            $editingRecord = null;
            if ($request->filled('record')) {
                $editingRecord = $records->firstWhere('id', (int) $request->get('record'));
            }

            $thirteenthMonthData = compact('calendarYear', 'records', 'editingRecord');
        }

        return view('hr.bonuses.edit', compact(
            'bonus',
            'employees',
            'formulaVariables',
            'thirteenthMonthData'
        ));
    }

    public function update(Request $request, Bonus $bonus)
    {
        if (! $bonus->isEditableBy(auth()->user())) {
            abort(403, 'Only administrators can edit mandatory system bonuses.');
        }

        $validated = $this->validateBonus($request, $bonus);

        if ($bonus->is_system_generated) {
            unset($validated['code']);
            if ($bonus->is_mandatory) {
                $validated['type'] = Bonus::TYPE_MANDATORY;
                $validated['is_mandatory'] = true;
            }
        }

        $bonus->update($validated);
        $this->syncEligibleEmployees($bonus, $request->input('eligible_employees', []));

        return redirect()
            ->route('bonuses.index')
            ->with('success', 'Bonus updated successfully.');
    }

    public function destroy(Bonus $bonus)
    {
        if (! $bonus->isDeletable()) {
            return redirect()
                ->route('bonuses.index')
                ->with('error', 'Mandatory or system bonuses cannot be deleted.');
        }

        if (! in_array(auth()->user()->role, ['hr', 'superadmin'], true)) {
            abort(403, 'Unauthorized action.');
        }

        $bonus->delete();

        return redirect()
            ->route('bonuses.index')
            ->with('success', 'Bonus deleted successfully.');
    }

    protected function validateBonus(Request $request, ?Bonus $bonus = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', [
                Bonus::TYPE_FIXED,
                Bonus::TYPE_PERCENTAGE,
                Bonus::TYPE_FORMULA,
                Bonus::TYPE_MANDATORY,
            ]),
            'computation_method' => 'required|in:' . implode(',', [
                Bonus::METHOD_MANUAL,
                Bonus::METHOD_AUTO,
            ]),
            'formula' => 'nullable|string|max:2000',
            'fixed_amount' => 'nullable|numeric|min:0',
            'percentage_value' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'year' => 'nullable|integer|min:2000|max:2100',
            'payroll_period' => 'nullable|string|max:100',
            'eligible_employees' => 'nullable|array',
            'eligible_employees.*' => 'exists:users,id',
        ];

        if (! $bonus?->is_system_generated) {
            $rules['code'] = 'nullable|string|max:100|unique:bonuses,code' . ($bonus ? ',' . $bonus->id : '');
        }

        $validated = $request->validate($rules);

        if ($validated['computation_method'] === Bonus::METHOD_AUTO) {
            if (empty($validated['formula'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'formula' => 'Formula is required when using automatic computation.',
                ]);
            }

            try {
                $this->formulaEngine->validate($validated['formula']);
            } catch (InvalidArgumentException $e) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'formula' => $e->getMessage(),
                ]);
            }
        }

        if ($validated['type'] === Bonus::TYPE_PERCENTAGE && $validated['computation_method'] === Bonus::METHOD_MANUAL) {
            if (empty($validated['percentage_value'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'percentage_value' => 'Percentage value is required for percentage-based bonuses.',
                ]);
            }
        }

        if ($validated['type'] === Bonus::TYPE_FIXED && $validated['computation_method'] === Bonus::METHOD_MANUAL) {
            if (empty($validated['fixed_amount'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fixed_amount' => 'Fixed amount is required for fixed-amount bonuses.',
                ]);
            }
        }

        return $validated;
    }

    protected function syncEligibleEmployees(Bonus $bonus, array $employeeIds): void
    {
        $employeeIds = array_filter(array_map('intval', $employeeIds));

        if (empty($employeeIds)) {
            $bonus->eligibleEmployees()->detach();

            return;
        }

        $bonus->eligibleEmployees()->sync($employeeIds);
    }

    protected function employeeOptions()
    {
        return User::query()
            ->whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'name', 'first_name', 'last_name', 'position', 'department', 'status']);
    }
}
