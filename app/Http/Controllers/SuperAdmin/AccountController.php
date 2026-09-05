<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PositionRate;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'account');

        $users = User::orderBy('created_at', 'desc')->paginate(15);
        $allUsers = User::orderBy('created_at', 'desc')->get();

        return view('superadmin.accounts.index', compact('users', 'allUsers'));
    }

    public function create()
    {
        $positions = PositionRate::where('is_active', true)
            ->with('department')
            ->orderBy('name')
            ->get();
        $shifts = Shift::where('is_active', true)->orderBy('name')->get();

        return view('superadmin.accounts.create', compact('positions', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:hr,accountant,remittance_clerk,employee,qr_admin',
            'phone' => ['nullable', 'regex:/^09\d{9}$/'],
            'position' => 'required|string|max:255',
            'shift_id' => 'required|exists:shifts,id',
            'department' => 'nullable|string|max:255',
            'date_of_hire' => 'required|date',
            'salary_rate' => 'required|numeric|min:0',
        ], [
            'position.required' => 'Position is required.',
            'date_of_hire.required' => 'Date of hire is required.',
            'salary_rate.required' => 'Daily rate is required.',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        $validated['department'] = $this->resolveDepartmentFromPosition($validated['position'] ?? '');

        $user = User::create($validated);

        $this->logActivity('created', 'account', null, 'App\Models\User', $user->id);

        return redirect()->route('superadmin.accounts.index')->with('success', 'Account created successfully!');
    }

    public function edit(User $account)
    {
        $shifts = Shift::where('is_active', true)->orderBy('name')->get();

        return view('superadmin.accounts.edit', ['user' => $account, 'shifts' => $shifts]);
    }

    public function update(Request $request, User $account)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $account->id,
            'role' => 'required|string|in:superadmin,hr,accountant,remittance_clerk,employee,qr_admin',
            'phone' => ['nullable', 'regex:/^09\d{9}$/'],
            'position' => 'nullable|string|max:255',
            'shift_id' => 'required|exists:shifts,id',
            'department' => 'nullable|string|max:255',
            'date_of_hire' => 'nullable|date',
            'salary_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $validated['department'] = $this->resolveDepartmentFromPosition($validated['position'] ?? '');

        $account->update($validated);

        $this->logActivity('updated', 'account', null, 'App\Models\User', $account->id);

        return redirect()->route('superadmin.accounts.index')->with('success', 'Account updated successfully!');
    }

    public function showResetPassword(User $account)
    {
        return view('superadmin.accounts.reset-password', ['user' => $account]);
    }

    public function performResetPassword(User $account)
    {
        $base = preg_replace('/[^a-zA-Z]/', '', $account->first_name ?? '') ?: 'User';
        $base = ucfirst(strtolower(substr($base, 0, 6)));
        $suffix = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $tempPassword = $base . $suffix;
        $account->update([
            'password' => Hash::make($tempPassword),
            'password_changed' => false,
        ]);

        return redirect()->route('superadmin.accounts.reset-password', $account)
            ->with('reset_password', $tempPassword);
    }

    public function toggleStatus(Request $request, User $account)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive',
        ]);

        $account->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $account->status,
        ]);
    }

    private function resolveDepartmentFromPosition(string $position): ?string
    {
        $positionRate = PositionRate::with('department')
            ->where('name', $position)
            ->first();

        return $positionRate?->department?->name;
    }
}
