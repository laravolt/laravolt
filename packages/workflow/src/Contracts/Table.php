<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Contracts;

interface Table
{
    public function source($sqlOnly = false);
}
