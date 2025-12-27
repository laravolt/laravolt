<?php

declare(strict_types=1);

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Laravolt\Middleware\CheckPassword;
use Laravolt\Middleware\DetectFlashMessage;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [
            DetectFlashMessage::class,
            CheckPassword::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();

        $exceptions->reportable(function (Throwable $e): void {
            if (app()->bound('sentry')) {
                /** @var object $sentry */
                $sentry = resolve('sentry');
                /** @phpstan-ignore method.notFound */
                $sentry->captureException($e);
            }
        });

        $exceptions->render(fn (TokenMismatchException $e) => back()->with(
            'error',
            __('Kami mendeteksi tidak ada aktivitas cukup lama, silakan kirim ulang form.')
        ));

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }

            return redirect()
                ->guest($e->redirectTo($request) ?? route('auth::login.show'))
                ->with('warning', __('Silakan login terlebih dahulu'));
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            if ($request->is('livewire/*')) {
                return abort(403);
            }

            return back(302, [], route('home'))
                ->withInput()
                ->with('error', $e->getMessage());
        });
    })->create();
