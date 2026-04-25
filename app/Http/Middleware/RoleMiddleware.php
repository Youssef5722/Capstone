<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Ensure the authenticated user has the required role.
     * Uses null-safe access on the role relation so a user with a
     * missing role_id never causes a fatal TypeError.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (!$user || $user->role?->name !== $role) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
