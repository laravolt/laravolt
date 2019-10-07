<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Http\Request;

class Login implements \Laravolt\Contracts\Login
{
    public function rules(Request $request)
    {
        $rules = [
            $this->identifier() => 'required',
            'password'          => 'required',
        ];

        if (config('laravolt.auth.captcha')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return $rules;
    }

    public function credentials(Request $request)
    {
        $credential = $request->only($this->identifier(), 'password');

        if (config('laravolt.auth.activation.enable')) {
            $credential['status'] = config('laravolt.auth.activation.status_after');
        }

        return $credential;
    }

    public function loggedOut(Request $request)
    {
        return redirect()->to(config('laravolt.auth.redirect.after_logout', '/'));
    }

    protected function identifier()
    {
        return config('laravolt.auth.identifier');
    }
}
