<?php

namespace Laravolt\Suitable\Contracts;

interface Header
{
    public function setAttributes(array $attributes): self;

    public function render(): string;
}
