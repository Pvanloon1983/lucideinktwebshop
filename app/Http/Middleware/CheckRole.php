<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // Redirect instead of returning a raw view to ensure a proper Response instance
            $target = Route::has('dashboard') ? route('dashboard') : url('/');
            return redirect()->to($target)->with('error', 'Onvoldoende rechten.');
        }
        return $next($request);
    }
}
