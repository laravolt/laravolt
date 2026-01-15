<?php

declare(strict_types=1);

namespace Laravolt\Asset;

use Illuminate\Support\Facades\Facade;
use RuntimeException;

class AssetFacade extends Facade
{
    public static function group($group = 'default')
    {
        $binding = "laravolt.asset.group.$group";

        if (! static::$app->bound($binding)) {
            throw new RuntimeException("Assets group '$group' not found in the config file (config/laravolt/asset.php)");
        }

        return static::$app[$binding];
    }

    protected static function getFacadeAccessor()
    {
        return 'laravolt.asset.group.default';
    }
}
