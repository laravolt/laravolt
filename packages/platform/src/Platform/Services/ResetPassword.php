<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

class ResetPassword implements \Laravolt\Contracts\ForgotPassword
{
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    public function getUserByIdentifier($identifier)
    {
        $identifierColumn = config('laravolt.auth.password.reset.identifier') ?? config('laravolt.auth.identifier');

        return app(config('auth.providers.users.model'))
            ->query()
            ->where($identifierColumn, '=', $identifier)->first();
    }
}
