<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Contracts;

interface Header
{
    public function setAttributes(array $attributes): self;

    public function getAttributesString(): string;

    public function getContent(): string;

    public function render(): string;
}
