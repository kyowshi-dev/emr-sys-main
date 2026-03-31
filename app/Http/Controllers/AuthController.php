<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show the Login Form
    public function showLogin()
    {
        // If user is already logged in, send them to the dashboard (or patients list)
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        // 1. Validate the Input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 2. Attempt to Log In
        // specific 'remember' logic handles the checkbox
        $remember = $request->has('remember');

        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {

            // 3. Security: Regenerate Session ID
            // (Prevents session fixation attacks)
            $request->session()->regenerate();

            // 4. Redirect User
            // 'intended' sends them to the URL they tried to visit before being intercepted by login
            // Default fallback is 'dashboard'
            return redirect()->intended(route('dashboard'));
        }

        // 5. If Login Fails...
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session (Security best practice)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function submitForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
        ]);

        $user = \App\Models\User::where('username', $validated['username'])->first();

        $requestRecord = \App\Models\PasswordResetRequest::create([
            'user_id' => $user?->id,
            'username_requested' => $validated['username'],
            'status' => 'pending',
        ]);

        // Simple non-email notification: tracked in DB for admins.
        // Optionally this can be replaced with real notifications when available.

        return redirect()->route('login')->with('success', 'Password reset request submitted. An administrator will be notified and will assist you with your login.');
    }
}
