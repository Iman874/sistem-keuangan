<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Clear any stale session data that might be causing issues
        if ($request->session()->has('url.intended')) {
            $request->session()->forget('url.intended');
        }

        return $request->expectsJson() ? null : route('login');
    }
}
