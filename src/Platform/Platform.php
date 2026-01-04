<?php

declare(strict_types=1);

namespace Laravolt\Platform;

use Illuminate\Support\Facades\Facade;

class Platform extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'laravolt.platform';
    }
}
