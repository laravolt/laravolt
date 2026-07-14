<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Contracts\CanChangePassword as CanChangePasswordContract;
use Laravolt\Contracts\CanResetPassword as CanResetPasswordContact;
use Laravolt\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Laravolt\Platform\Concerns\CanChangePassword;
use Laravolt\Platform\Concerns\CanResetPassword;
use Laravolt\Platform\Concerns\HasRoleAndPermission;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends BaseUser implements CanChangePasswordContract, CanResetPasswordContact, HasMedia, HasRoleAndPermissionContract, MustVerifyEmail
{
    use CanChangePassword;
    use CanResetPassword;
    use HasRoleAndPermission;
    use HasUlids;
    use InteractsWithMedia;

    protected $guarded = [];

    public function getAvatarAttribute()
    {
        $avatar = null;

        if (! $avatar && app()->bound('avatar')) {
            /**
             * @var \Laravolt\Avatar\Avatar $service
             */
            $service = app('avatar');
            try {
                $avatar = $service->create($this->name)->toBase64();
            } catch (\Throwable $e) {
                // Defensive fallback: prevent UI crash if avatar generation fails
                // (e.g. intervention/image v4 vertical alignment bug)
                $avatar = null;
            }
        }

        if (! $avatar) {
            $avatar = asset('laravolt/img/default/avatar.png');
        }

        return $avatar;
    }
}
