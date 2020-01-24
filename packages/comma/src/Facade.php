<?php

namespace Laravolt\Comma;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'laravolt.comma';
    }
}
