<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Auth;

use Illuminate\Support\Facades\Cache;
use Laravolt\Platform\Models\User;

class UserObserver
{
    protected int $ttl = 3600;

    public function saved(User $user)
    {
        Cache::put("users.{$user->getKey()}", $user, $this->ttl);
    }

    public function deleted(User $user)
    {
        Cache::forget("users.{$user->getKey()}");
    }

    public function restored(User $user)
    {
        Cache::put("users.{$user->getKey()}", $user, 60);
    }
}
