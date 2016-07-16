<?php
namespace Laravolt\Suitable\Contracts;

use Laravolt\Suitable\Builder;

interface Component
{
    public function boot(Builder $builder);

    public function header();

    public function cell($cell);

}
