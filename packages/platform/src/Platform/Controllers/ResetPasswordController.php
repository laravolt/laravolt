<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    use ValidatesRequests;
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        $this->redirectTo = config('laravolt.auth.redirect.after_reset_password');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('laravolt::reset')->with(
            ['token' => $token, 'email' => urldecode($request->email)]
        );
    }

    public function reset(Request $request)
    {
        $this->validate($request, app('laravolt.auth.password.reset')->rules());

        $identifierColumn = config('laravolt.auth.password.reset.identifier') ?? config('laravolt.auth.identifier');
        $user = app('laravolt.auth.password.reset')->getUserByIdentifier($request->get($identifierColumn));

        $response = app('laravolt.password')->changePasswordByToken(
            $user,
            $request->password,
            $request->token
        );

        if ($response == Password::PASSWORD_RESET) {
            if (config('laravolt.auth.password.reset.auto_login')) {
                auth()->login($user);
            }

            return $this->sendResetResponse($request, $response);
        }

        return $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('success', trans($response));
    }
}
