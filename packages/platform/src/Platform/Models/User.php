<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;
use Laravolt\Platform\Concerns\HasRoleAndPermission;
use Laravolt\Contracts\CanChangePassword as CanChangePasswordContract;
use Laravolt\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class User extends BaseUser implements CanChangePasswordContract, HasMedia, HasRoleAndPermissionContract
{
    use CanChangePassword;
    use CanResetPassword;
    use HasMediaTrait;
    use HasRoleAndPermission;

    protected $guarded = [];
}
