<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Driver;

class DriverAuthController extends Controller
{
    /**
     * Show the driver login form
     */
    public function showLoginForm()
    {
        return view('driver.auth.login');
    }

    /**
     * Handle driver login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if ($driver && Hash::check($request->password, $driver->password)) {
            // Log the driver in using the 'driver' guard
            Auth::guard('driver')->login($driver, $request->has('remember'));

            return redirect()->intended(route('driver.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle driver logout
     */
    public function logout(Request $request)
    {
        Auth::guard('driver')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('driver.login');
    }
}