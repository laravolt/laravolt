<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;

class User extends BaseUser implements \Laravolt\Platform\Contracts\CanChangePassword
{
    use CanChangePassword;
    use CanResetPassword;

    protected $guarded = [];
}
