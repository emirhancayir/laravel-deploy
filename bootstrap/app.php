<?php

use App\Http\Middleware\SellerMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckIpBan;
use App\Http\Middleware\CleanInvalidUploads;
use App\Http\Middleware\CheckMaintenanceMode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Clean invalid file uploads early
        $middleware->prepend(CleanInvalidUploads::class);

        // Maintenance mode check - append to web middleware group
        $middleware->web(append: [
            CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'seller' => SellerMiddleware::class,
            'admin' => AdminMiddleware::class,
            'permission' => CheckPermission::class,
            'ip.ban' => CheckIpBan::class,
            'maintenance' => CheckMaintenanceMode::class,
        ]);

        // Redirect unauthenticated users to /login page
        $middleware->redirectGuestsTo(function (Request $request) {
            // For AJAX requests, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                abort(401, 'Oturum açmanız gerekiyor');
            }
            return '/login';
        });

        // Disable CSRF verification for iyzico callback
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
            'payment/result',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // File upload error - temporary file not found
        $exceptions->render(function (FileNotFoundException $e, Request $request) {
            if (str_contains($e->getMessage(), '/tmp/php')) {
                return redirect()->back()
                    ->withInput($request->except('resimler'))
                    ->with('hata', 'Dosya yükleme hatası oluştu. Lütfen resimleri tekrar seçin ve formu gönderin.');
            }
        });

        // JSON error response for AJAX requests
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen giriş yapın',
                ], 401);
            }
        });
    })->create();
