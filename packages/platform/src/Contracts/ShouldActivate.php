<?php

namespace Laravolt\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ShouldActivate
{
    public function notify(Model $user, $token);

    public function activate($token);
}
