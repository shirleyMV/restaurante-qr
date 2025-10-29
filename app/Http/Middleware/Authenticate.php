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
        // Si es una petición al admin, redirigir al login de Filament
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('filament.admin.auth.login');
        }
        
        // Para otras rutas, redirigir al home
        return $request->expectsJson() ? null : url('/');
    }
}
