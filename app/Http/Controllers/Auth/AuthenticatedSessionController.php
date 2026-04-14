<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Try normal user login first (default web guard).
        try {
            $request->authenticate();
            $request->session()->regenerate();

            return redirect()->intended('/store');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If user auth fails, try admin guard using same credentials.
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                $request->session()->regenerate();

                return redirect()->intended('/admin/dashboard');
            }

            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
