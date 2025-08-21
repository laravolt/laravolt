<?php

declare(strict_types=1);

namespace Laravolt\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\View\Factory;

class ContentSecurityPolicy
{
    public function __construct(private readonly Application $app)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $nonce = bin2hex(random_bytes(16));

        // Store nonce on the request and share to views
        $request->attributes->set('csp_nonce', $nonce);
        view()->share('cspNonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        // Do not override if header already exists
        if ($response->headers->has('Content-Security-Policy') || $response->headers->has('Content-Security-Policy-Report-Only')) {
            return $response;
        }

        $directives = config('laravolt.csp.directives', []);

        // Skip if no directives configured
        if (empty($directives) || !is_array($directives)) {
            return $response;
        }

        // Replace {nonce} placeholder
        $compiled = [];
        foreach ($directives as $name => $value) {
            if (is_array($value)) {
                $value = array_map(static function ($item) use ($nonce) {
                    return str_replace('{nonce}', $nonce, (string) $item);
                }, $value);
                $compiled[] = $name.' '.implode(' ', $value);
            } elseif (is_string($value) && $value !== '') {
                $compiled[] = $name.' '.str_replace('{nonce}', $nonce, $value);
            } else {
                // directives without values, e.g., upgrade-insecure-requests
                $compiled[] = $name;
            }
        }

        $policy = trim(implode('; ', $compiled));

        $headerName = config('laravolt.csp.report_only', true)
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $response->headers->set($headerName, $policy);

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        $except = (array) config('laravolt.csp.except', []);
        foreach ($except as $pattern) {
            if ($pattern && $request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}