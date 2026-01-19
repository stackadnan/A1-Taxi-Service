<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt login using either email or username
        $attempted = false;
        if (! empty($credentials['email'])) {
            $attempted = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember);
        }

        if (! $attempted && ! empty($credentials['username'])) {
            $attempted = Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $remember);
        }

        if ($attempted) {
            // record login info (ip, user agent) and update user's last login
            $user = Auth::user();
            $ip = $request->ip();

            try {
                \Illuminate\Support\Facades\DB::table('user_login_logs')->insert([
                    'user_id' => $user->id,
                    'ip_address' => $ip,
                    'user_agent' => $request->userAgent(),
                    'logged_in_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // set attributes directly to avoid mass-assignment restrictions
                $user->last_login_at = now();
                $user->last_login_ip = $ip;
                $user->save();
            } catch (\Exception $e) {
                // don't block login on logging failure, but report in logs
                logger()->error('Failed to write login log: ' . $e->getMessage());
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['username' => 'The provided credentials do not match our records.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
