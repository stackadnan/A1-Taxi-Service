<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('admin_authenticated', false) === true) {
            return redirect()->route('admin.pages.index');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $expectedUsername = (string) config('admin_auth.username', 'admin');
        $expectedPassword = (string) config('admin_auth.password', 'secret');

        if (
            hash_equals($expectedUsername, $credentials['username'])
            && hash_equals($expectedPassword, $credentials['password'])
        ) {
            $request->session()->regenerate();
            $request->session()->put('admin_authenticated', true);

            return redirect()->intended(route('admin.pages.index'));
        }

        return back()
            ->withErrors(['username' => 'Invalid username or password.'])
            ->withInput($request->only('username'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('status', 'Logged out successfully.');
    }
}
