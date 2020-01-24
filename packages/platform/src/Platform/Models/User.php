<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Contracts\CanChangePassword as CanChangePasswordContract;
use Laravolt\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;
use Laravolt\Platform\Concerns\HasRoleAndPermission;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class User extends BaseUser implements CanChangePasswordContract, HasMedia, HasRoleAndPermissionContract
{
    use CanChangePassword;
    use CanResetPassword;
    use HasMediaTrait;
    use HasRoleAndPermission;

    protected $guarded = [];

    public function getAvatarAttribute()
    {
        $avatar = null;

        if ($this instanceof HasMedia) {
            $avatar = $this->getFirstMediaUrl('avatar');
        }

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
