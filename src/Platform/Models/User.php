<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Foundation\Auth\User as BaseUser;
use Laravolt\Avatar\Avatar;
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

    /**
     * Generated avatars by name and avatar config, for the life of the
     * process: an Octane worker shows the same few users on every page.
     *
     * @var array<string, string>
     */
    private static array $generatedAvatars = [];

    public function getAvatarAttribute()
    {
        $avatar = null;

        if (! $avatar && app()->bound('avatar')) {
            // The same name always gets the same avatar (laravolt/avatar picks
            // the colours from the name), so generating it, or reading it back
            // from the cache store, once per page is wasted work.
            $name = (string) $this->name;
            $key = $name === '' ? null : $name."\0".serialize(config('laravolt.avatar'));

            if ($key !== null && isset(self::$generatedAvatars[$key])) {
                return self::$generatedAvatars[$key];
            }

            /** @var Avatar */
            $service = app('avatar');
            $avatar = $service->create($this->name)->toBase64();

            if ($key !== null && $avatar) {
                if (count(self::$generatedAvatars) >= 100) {
                    self::$generatedAvatars = [];
                }

                self::$generatedAvatars[$key] = $avatar;
            }
        }

        if (! $avatar) {
            $avatar = asset('laravolt/img/default/avatar.png');
        }

        return $avatar;
    }
}
