<?php
namespace Laravolt\SemanticForm;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'semantic-form';
    }
}
