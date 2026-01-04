<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Auth;

use Cache;
use Illuminate\Auth\EloquentUserProvider;
use Log;

class CacheEloquentUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        // save users for one hour (3600 seconds)
        return Cache::remember("users.$identifier", 3600, function () use ($identifier) {
            Log::info($identifier);

            return parent::retrieveById($identifier);
        });
    }
}
