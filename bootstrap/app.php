<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Cookie preferensi UI dibuat oleh JavaScript (tidak terenkripsi),
        // jadi dikecualikan dari EncryptCookies agar bisa dibaca di Blade.
        // Isinya hanya: accepted/rejected dan open/collapsed (bukan data sensitif).
        $middleware->encryptCookies(except: [
            'ksp_cookie_consent',
            'ksp_sidebar_state',
        ]);

        // DAFTARKAN ALIAS PERMISSION DI SINI
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();