<?php

namespace Laravolt\PrelineForm;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'preline-form';
    }
}
