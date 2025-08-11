<?php

namespace Laravolt\Platform\Components;

class ComponentManager
{
    public static ?string $currentComponent = null;

    public static function reset(): void
    {
        self::$currentComponent = null;
    }
}
