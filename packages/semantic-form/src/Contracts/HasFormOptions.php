<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Contracts;

interface HasFormOptions
{
    public function toFormOptions(): array;
}
