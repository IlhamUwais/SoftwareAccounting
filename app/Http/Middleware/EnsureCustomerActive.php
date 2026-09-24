<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerActive
{
    /**
     * If a CUSTOMER user's parent Customer becomes INACTIVE (or gets
     * soft-deleted) while they still have an active session, force them
     * out immediately instead of trusting a stale session.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ! $user->canAccessApp()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun perusahaan Anda saat ini tidak aktif.']);
        }

        return $next($request);
    }
}
