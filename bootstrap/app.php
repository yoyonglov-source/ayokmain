<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'checkout/send-otp',  // Sesuaikan dengan URL route OTP kamu
        'checkout/verify-otp', // Sesuaikan dengan URL route verifikasi kamu
        'checkout/send-otp-email',
        'checkout/store',
        'api/xendit/callback'
        ]);
        $middleware->redirectTo(function (Request $request) {
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
