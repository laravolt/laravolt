<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            \Laravolt\Middleware\DetectFlashMessage::class,
            \Laravolt\Middleware\CheckPassword::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();

        $exceptions->reportable(function (\Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e) {
            return back()->with(
                'error',
                __('Kami mendeteksi tidak ada aktivitas cukup lama, silakan kirim ulang form.')
            );
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }

            return redirect()
                ->guest($e->redirectTo($request) ?? route('auth::login.show'))
                ->with('warning', __('Silakan login terlebih dahulu') ?? '');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            if ($request->is('livewire/*')) {
                return abort(403);
            }

            return redirect()
                ->back(302, [], route('home'))
                ->withInput()
                ->with('error', $e->getMessage());
        });
    })->create();
