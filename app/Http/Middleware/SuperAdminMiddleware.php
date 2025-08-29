<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
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

        // Only SuperAdmin can access
        if (!$user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied. SuperAdmin access required.'], 403);
            }

            return redirect()->back()->with('error', 'Access denied. SuperAdmin access required.');
        }

        return $next($request);
    }
}
