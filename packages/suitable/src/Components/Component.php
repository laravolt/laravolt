<?php
namespace Laravolt\Suitable\Components;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Contracts\Component as ComponentInterface;

abstract class Component implements ComponentInterface
{
    protected $builder;

    public function boot(Builder $builder)
    {
        $this->builder = $builder;
    }
}
