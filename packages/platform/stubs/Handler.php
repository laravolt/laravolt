<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()
                ->guest($exception->redirectTo() ?? route('auth::login'))
                ->withWarning(__('Silakan login terlebih dahulu'));
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     *
     * @throws \Exception
     *
     * @return void
     */
    public function report(\Throwable $e)
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable                $e
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            return back()->withError(__('Kami mendeteksi tidak ada aktivitas cukup lama, silakan ulangi aksi sebelumnya'));
        }

        if ($e instanceof AuthorizationException) {
            return redirect()->back(302, [], route('dashboard'))->withError(
                __('Anda tidak diizinkan mengakses halaman :url', ['url' => $request->fullUrl()])
            );
        }

        return parent::render($request, $e);
    }
}
