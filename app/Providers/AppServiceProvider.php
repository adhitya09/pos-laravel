<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

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
        // Blade helper: @perm('resource.action') to check role permissions
        Blade::if('perm', function ($ability) {
            $user = Auth::user();
            return $user && method_exists($user, 'hasPermission') && $user->hasPermission($ability);
        });

        // Make the `can` / Gate checks use the stored role permissions via User::hasPermission
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                // allow if the role provides this permission
                return $user->hasPermission($ability) ? true : null;
            }
            return null;
        });
    }
}
