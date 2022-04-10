<?php

namespace Laravolt\Epicentrum\Auth;

use Illuminate\Support\Facades\Cache;
use Laravolt\Platform\Models\User;

class UserObserver
{
    protected int $ttl = 3600;
    /**
     * @param User $user
     */
    public function saved(User $user)
    {
        Cache::put("users.{$user->getKey()}", $user, $this->ttl);
    }

    /**
     * @param User $user
     */
    public function deleted(User $user)
    {
        Cache::forget("users.{$user->getKey()}");
    }

    /**
     * @param User $user
     */
    public function restored(User $user)
    {
        Cache::put("users.{$user->getKey()}", $user, 60);
    }
}
