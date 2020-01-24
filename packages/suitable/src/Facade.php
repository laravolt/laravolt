<?php

namespace Laravolt\Suitable;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'laravolt.suitable';
    }
}
