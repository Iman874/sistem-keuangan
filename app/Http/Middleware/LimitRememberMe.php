<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimitRememberMe
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // If authenticated via remember cookie and timestamp exceeded 2 days
            if (Auth::viaRemember()) {
                $limitDays = 2; // configured duration
                if ($user->remember_logged_at && now()->diffInDays($user->remember_logged_at) >= $limitDays) {
                    Auth::logout();
                    return response()->view('errors.no_access', [
                        'redirectUrl' => route('login')
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
