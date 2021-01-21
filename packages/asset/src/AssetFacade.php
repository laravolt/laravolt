<?php

namespace Laravolt\Asset;

use Illuminate\Support\Facades\Facade;

class AssetFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravolt.asset.group.default';
    }

    public static function group($group = 'default')
    {
        $binding = "laravolt.asset.group.$group";

        if (! static::$app->bound($binding)) {
            throw new \RuntimeException("Assets group '$group' not found in the config file (config/laravolt/asset.php)");
        }

        return static::$app[$binding];
    }
}
