<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Contracts\CanChangePassword as CanChangePasswordContract;
use Laravolt\Contracts\CanResetPassword as CanResetPasswordContact;
use Laravolt\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;
use Laravolt\Platform\Concerns\HasRoleAndPermission;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends BaseUser implements CanChangePasswordContract, CanResetPasswordContact, HasRoleAndPermissionContract, HasMedia, MustVerifyEmail
{
    use CanChangePassword;
    use CanResetPassword;
    use HasRoleAndPermission;
    use InteractsWithMedia;

    protected $guarded = [];

    public function getAvatarAttribute()
    {
        $avatar = null;

        if (!$avatar) {
            if (app()->bound('avatar')) {
                $avatar = app('avatar')->create($this->name)->toBase64();
            }
        }

        if (!$avatar) {
            $avatar = asset('laravolt/img/default/avatar.png');
        }

        return $avatar;
    }
}
