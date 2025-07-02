<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isAdmin() && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('admin.verification.notice');
        }

        return $next($request);
    }
}