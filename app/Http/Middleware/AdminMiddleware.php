<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Force logout if user is inactive
        if ((int)($user->status ?? 1) === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account is inactive.'], 401);
            }
            return redirect()->route('admin.login')->withErrors(['inactive' => 'Your account is inactive.']);
        }

        // SuperAdmin can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Allow any user with a valid role (except Employee which shouldn't login)
        if ($user->roles()->exists() && !$user->hasRole('Employee')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        return redirect()->route('admin.login');
    }
}
