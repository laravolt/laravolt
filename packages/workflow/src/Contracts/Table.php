<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Workflow\Contracts;

interface Table
{
    public function source($sqlOnly = false);
}
