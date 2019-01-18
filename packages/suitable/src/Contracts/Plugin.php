<?php

namespace Laravolt\Suitable\Contracts;

use Laravolt\Suitable\Builder;

interface Plugin
{
    public function init();

    public function resolve($source);

    public function decorate(Builder $table): Builder;

    public function response();
}
