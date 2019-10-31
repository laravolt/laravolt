<?php

declare(strict_types=1);

namespace Laravolt\Platform\Http\Middleware;

use Closure;
use Illuminate\Support\ViewErrorBag;
use Laravolt\Platform\Services\Flash;

class FlashMiddleware
{
    /**
     * The Flash instance.
     *
     * @var Flash
     */
    protected $flash;

    /**
     * FlashMiddleware constructor.
     *
     * @param Flash $flash
     */
    public function __construct(Flash $flash)
    {
        $this->flash = $flash;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        // Skip flash for some routes registered in "except" config
        if ($this->flash->inExceptArray($request)) {
            return $response;
        }

        try {
            if ($request->session()->has('errors')) {
                $message = $request->session()->get('errors');

                if ($message instanceof ViewErrorBag) {
                    $message = collect($message->unique())->implode('<br />');
                }

                $this->flash->now()->error($message);
            }

            if ($message = $request->session()->get('success')) {
                $this->flash->success($message);
            }

            if ($message = $request->session()->get('warning')) {
                $this->flash->warning($message);
            }

            if ($message = $request->session()->get('info')) {
                $this->flash->info($message);
            }

            if ($message = $request->session()->get('message')) {
                $this->flash->message($message);
            }

            if ($message = $request->session()->get('error')) {
                $this->flash->error($message);
            }

            // Modify the response to add the Flash
            if (!$request->ajax() && $this->flash->hasMessage()) {
                $this->flash->injectScript($response);
            }
        } catch (Exception $e) {
            //@todo: handle error
        } finally {
            return $response;
        }
    }
}
