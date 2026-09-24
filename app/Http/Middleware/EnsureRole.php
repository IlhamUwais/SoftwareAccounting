<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage in routes: ->middleware('role:SUPERADMIN')
     * This is a coarse route-level gate. Object-level ownership checks
     * (does this SPT/purchase belong to this customer) still ALWAYS go
     * through the Policies - this middleware never replaces that.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless($request->user()?->role === $role, 403);

        return $next($request);
    }
}
