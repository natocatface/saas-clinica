<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Restringe el acceso a usuarios con uno de los roles indicados.
     * Uso en rutas: ->middleware('role:admin,recepcion')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'No tienes permiso para acceder a este modulo.');
        }

        // El administrador de la clinica tiene acceso a todo.
        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return $next($request);
        }

        if (count($roles) && ! in_array($user->role, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a este modulo.');
        }

        return $next($request);
    }
}
