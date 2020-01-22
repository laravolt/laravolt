<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

class ForgotPassword implements \Laravolt\Contracts\ForgotPassword
{
    public function rules()
    {
        return [
            'email' => ['email', 'required'],
        ];
    }

    public function getUserByIdentifier($identifier)
    {
        $identifierColumn = config('laravolt.auth.password.forgot.identifier') ?? config('laravolt.auth.identifier');

        return app(config('auth.providers.users.model'))
            ->query()
            ->where($identifierColumn, '=', $identifier)->first();
    }
}
