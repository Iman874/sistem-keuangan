<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
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
        return view('auth.custom-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            $request->session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();

            // If remember checkbox used, record timestamp for custom 2-day limit
            if ($request->boolean('remember')) {
                $user->remember_logged_at = now();
                $user->save();
            } else {
                // Clear previous remember timestamp when not using remember to avoid unintended auto login beyond session
                $user->remember_logged_at = null;
                $user->save();
            }
            
            switch ($user->role) {
                case 'kasir':
                    return redirect()->route('kasir.dashboard');
                case 'owner':
                    return redirect()->route('owner.dashboard');
                default:
                    return redirect(RouteServiceProvider::HOME);
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'credential' => 'The provided credentials do not match our records.',
            ])->withInput($request->except('password'));
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