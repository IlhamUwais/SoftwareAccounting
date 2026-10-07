<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP is production-only. In development, Vite's dev server injects
        // a script tag and opens a websocket from ITS OWN origin for
        // hot-reload (http(s)://HOST:5173), and that host varies by
        // machine/network - "localhost" can resolve to 127.0.0.1 or the
        // IPv6 loopback [::1] depending on the OS, a Docker container has
        // its own hostname, etc. Trying to allowlist every possible dev
        // origin is a losing game and a strict CSP fails silently (blank
        // page, nothing visible except in devtools), so it's simplest and
        // safest to only enforce CSP where the asset origins are fixed and
        // known: the production build served from the app's own domain.
        if (app()->environment('production')) {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data:",
                "connect-src 'self'",
                "frame-ancestors 'none'",
            ]);

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
