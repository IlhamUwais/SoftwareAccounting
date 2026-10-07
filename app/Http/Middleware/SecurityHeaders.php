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

        // Livewire injects its own inline bootstrap script, so script-src
        // needs 'unsafe-inline'. Chart.js loads from jsdelivr; Inter font
        // loads from Google Fonts. In local/debug, Vite's dev server also
        // injects a script tag and opens a websocket from its own origin
        // (http(s)://HOST:5173 and ws(s)://HOST:5173) for hot-reload - a
        // strict CSP blocks that silently (blank page, no visible error),
        // so the dev server's origin is only added outside production.
        $scriptSrc = "'self' 'unsafe-inline' https://cdn.jsdelivr.net";
        $connectSrc = "'self'";
        $styleSrc = "'self' 'unsafe-inline' https://fonts.googleapis.com";

        if (! app()->environment('production')) {
            $viteOrigin = $this->viteDevOrigin();
            $scriptSrc .= " {$viteOrigin}";
            $styleSrc .= " {$viteOrigin}";
            $connectSrc .= " {$viteOrigin} ".str_replace('http', 'ws', $viteOrigin);
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src {$scriptSrc}",
            "style-src {$styleSrc}",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data:",
            "connect-src {$connectSrc}",
            "frame-ancestors 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    private function viteDevOrigin(): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return "http://{$host}:5173 http://127.0.0.1:5173 http://localhost:5173";
    }
}
