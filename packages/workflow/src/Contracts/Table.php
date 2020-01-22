<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Contracts;

interface Table
{
    public function source($sqlOnly = false);
}
