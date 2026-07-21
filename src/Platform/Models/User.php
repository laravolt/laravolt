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
            /** @var \Laravolt\Avatar\Avatar */
            $service = app('avatar');
            try {
                $avatar = $service->create($this->name)->toBase64();
            } catch (\Intervention\Image\Exceptions\InvalidArgumentException $e) {
                // Laravolt\Avatar\Avatar passes 'middle' for vertical alignment
                // which throws an InvalidArgumentException in Intervention Image 4.2.0+.
                // Fall back to default avatar.
                $avatar = null;
            }
        }

        if (! $avatar) {
            $avatar = asset('laravolt/img/default/avatar.png');
        }

        return $avatar;
    }
}
