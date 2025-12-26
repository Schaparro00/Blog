<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Si es API (espera JSON), NO redirigir a ninguna ruta web.
        if ($request->expectsJson()) {
            return null;
        }

        // Si algún día usas web/blade, esto sirve para redirigir.
        return route('login');
    }
}
