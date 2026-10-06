<?php

use App\Http\Middleware\EnsureCustomerActive;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureRole::class,
            'ensure.customer.active' => EnsureCustomerActive::class,
        ]);

        // AuthenticateSession lets Auth::logoutOtherDevices() (used after a
        // password change) actually take effect on the next request from
        // any other active session.
        $middleware->web(append: [
            SecurityHeaders::class,
            AuthenticateSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
