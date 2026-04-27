<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);

        return view('superadmin.accounts.index', compact('users'));
    }

    public function create()
    {
        return view('superadmin.accounts.create');
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
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'date_of_hire' => 'nullable|date',
            'salary_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        User::create($validated);

        return redirect()->route('superadmin.accounts.index')->with('success', 'Account created successfully!');
    }

    public function edit(User $account)
    {
        return view('superadmin.accounts.edit', ['user' => $account]);
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
            'department' => 'nullable|string|max:255',
            'date_of_hire' => 'nullable|date',
            'salary_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $account->update($validated);

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
}
