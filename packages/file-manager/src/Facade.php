<?php

namespace Laravolt\FileManager;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'laravolt.file-manager';
    }
}
