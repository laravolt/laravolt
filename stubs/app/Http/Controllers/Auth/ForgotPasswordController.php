<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravolt\Contracts\CanResetPassword;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show()
    {
        return view('auth.forgot');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users']]);

        $response = Password::INVALID_USER;

        /** @var \Laravolt\Contracts\CanResetPassword $user */
        $user = User::whereEmail($request->email)->first();

        if ($user instanceof CanResetPassword) {
            $response = app('laravolt.password')->sendResetLink($user);
        }

        if ($response === Password::RESET_LINK_SENT) {
            $email = $user->getEmailForPasswordReset();

            return redirect()
                ->route('auth::forgot.show')
                ->with('success', trans($response, ['email' => $email, 'emailMasked' => Str::maskEmail($email)]));
        }

        return redirect()
            ->route('auth::forgot.show')
            ->with('error', ['email' => trans($response)]);
    }
}
