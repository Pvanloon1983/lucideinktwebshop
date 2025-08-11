<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        // Blade directive: @role('admin') ... @endrole
        Blade::if('role', function (string $role): bool {
            $user = auth()->user();
            return $user && $user->role === $role;
        });

        // Blade directive: @anyrole('admin','user') ... @endanyrole
        Blade::if('anyrole', function (string ...$roles): bool {
            $user = auth()->user();
            return $user && in_array($user->role, $roles, true);
        });
    }
}
