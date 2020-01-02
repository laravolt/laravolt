<?php
namespace Laravolt\Suitable;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'laravolt.suitable';
    }
}
