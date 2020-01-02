<?php

namespace Laravolt\Suitable\Contracts;

interface Header
{
    public function setAttributes(array $attributes): Header;

    public function render(): string;
}
