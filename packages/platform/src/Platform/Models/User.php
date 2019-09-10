<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;
use Laravolt\Platform\Contracts\CanChangePassword as CanChangePasswordContract;
use Laravolt\Platform\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Laravolt\Platform\Concerns\HasRoleAndPermission;

class User extends BaseUser implements CanChangePasswordContract, HasRoleAndPermissionContract
{
    use CanChangePassword;
    use CanResetPassword;
    use HasRoleAndPermission;

    protected $guarded = [];
}
