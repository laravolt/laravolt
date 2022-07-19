<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    protected function unauthenticated(
        $request,
        AuthenticationException $exception
    ) {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()
                ->guest($exception->redirectTo() ?? route('auth::login.show'))
                ->with('warning', __('Silakan login terlebih dahulu') ?? '');
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    public function report(\Throwable $e)
    {
        if ($this->shouldReport($e) && app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, \Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            return back()->with(
                'error',
                __('Kami mendeteksi tidak ada aktivitas cukup lama, silakan kirim ulang form.')
            );
        }

        if ($e instanceof AuthorizationException) {
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
        }

        return parent::render($request, $e);
    }
}
