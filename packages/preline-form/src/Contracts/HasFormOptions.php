<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Contracts;

interface HasFormOptions
{
    public function toFormOptions(): array;
}
