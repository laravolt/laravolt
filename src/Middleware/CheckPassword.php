<?php

declare(strict_types=1);

namespace Laravolt\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravolt\Contracts\CanChangePassword;

class CheckPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guest() || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        $authUser = auth()->user();
        if (($authUser instanceof CanChangePassword) && $authUser->passwordMustBeChanged(config('laravolt.password.duration'))) {
            return redirect(config('laravolt.password.redirect'))->withWarning(trans('laravolt::password.must_change_password'));
        }

        return $next($request);
    }

    protected function shouldPassThrough(Request $request): bool
    {
        $except = array_merge((array) config('laravolt.password.except'), (array) config('laravolt.password.redirect'));

        foreach ($except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
