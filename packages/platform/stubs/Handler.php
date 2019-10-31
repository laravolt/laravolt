<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return back()->withError(__('Kami mendeteksi tidak ada aktivitas cukup lama, silakan ulangi aksi sebelumnya'));
        }

        if ($exception instanceof AuthorizationException) {
            return redirect()->back(302, [], route('dashboard'))->withError(
                __('Anda tidak diizinkan mengakses halaman :url', ['url' => $request->fullUrl()])
            );
        }

        return parent::render($request, $exception);
    }
}
