<?php

declare(strict_types=1);

namespace Laravolt\Camunda;

use Laravolt\Support\Base\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'camunda';
    }
}
