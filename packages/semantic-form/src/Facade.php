<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'semantic-form';
    }
}
