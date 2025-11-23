<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            // Show friendly no access page then redirect
            return response()->view('errors.no_access', [
                'redirectUrl' => route('login')
            ], 403);
        }

        $roles = explode('|', $role);
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Authenticated but wrong role -> show no access page then redirect to login
        return response()->view('errors.no_access', [
            'redirectUrl' => route('login')
        ], 403);
    }
}