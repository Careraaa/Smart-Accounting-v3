<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validate the input
        $request->validate([
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!\Illuminate\Support\Facades\Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        // Update the password - the model cast 'hashed' will automatically hash it
        $user->password = $request->password;
        $user->password_changed = true;
        $user->save();

        return redirect()->route('settings.account')->with('success', 'Password updated successfully.');
    }
}
