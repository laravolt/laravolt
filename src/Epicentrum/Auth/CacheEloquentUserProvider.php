<?php

namespace Laravolt\Epicentrum\Auth;

use Illuminate\Auth\EloquentUserProvider;

class CacheEloquentUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        // save users for one hour (3600 seconds)
        return \Cache::remember("users.$identifier", 3600, function () use ($identifier) {
            \Log::info($identifier);
            return parent::retrieveById($identifier);
        });
    }
}
