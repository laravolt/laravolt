<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('laravolt::forgot');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, app('laravolt.auth.password.forgot')->rules());

        $identifierColumn = config('laravolt.auth.password.forgot.identifier') ?? config('laravolt.auth.identifier');
        $user = app('laravolt.auth.password.forgot')->getUserByIdentifier($request->get($identifierColumn));

        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $response = Password::INVALID_USER;
        if ($user) {
            $response = app('laravolt.password')->sendResetLink($user);
        }

        if ($response === Password::RESET_LINK_SENT) {
            $email = $user->getEmailForPasswordReset();

            return back()->withSuccess(trans($response, ['email' => $email, 'emailMasked' => Str::maskEmail($email)]));
        }

        // If an error was returned by the password broker, we will get this message
        // translated so we can notify a user of the problem. We'll redirect back
        // to where the users came from so they can attempt this process again.
        return back()->withErrors(
            ['email' => trans($response)]
        );
    }
}
