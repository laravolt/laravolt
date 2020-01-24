<?php

namespace Laravolt\Suitable\Contracts;

use Laravolt\Suitable\Builder;

interface Plugin
{
    public function init();

    public function resolve($source);

    public function decorate(Builder $table): Builder;

    public function filter($columns);

    public function shouldResponse(): bool;

    public function response($source, Builder $table);
}
