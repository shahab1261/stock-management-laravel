<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade directive to check if user has permission
        Blade::if('permission', function ($permission) {
            if (!Auth::check()) {
                return false;
            }

            $user = Auth::user();

            // SuperAdmin has all permissions
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->hasPermissionTo($permission);
        });

        // Blade directive to check if user has role
        Blade::if('role', function ($role) {
            if (!Auth::check()) {
                return false;
            }

            $user = Auth::user();

            // SuperAdmin has all roles
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->hasRole($role);
        });

        // Blade directive to check if user is SuperAdmin
        Blade::if('superadmin', function () {
            if (!Auth::check()) {
                return false;
            }

            return Auth::user()->isSuperAdmin();
        });
    }
}
