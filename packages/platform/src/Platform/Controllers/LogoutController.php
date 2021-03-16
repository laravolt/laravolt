<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $loginAction = app('laravolt.auth.login');
        if (method_exists($loginAction, 'loggedOut')) {
            $response = $loginAction->loggedOut($request);

            if ($response instanceof RedirectResponse) {
                return $response;
            }
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
