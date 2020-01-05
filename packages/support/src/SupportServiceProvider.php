<?php

declare(strict_types=1);

namespace Laravolt\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Laravolt\Support\Mixin\QueryBuilderMixin;

class SupportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerMacro();
    }

    public function boot()
    {
    }

    protected function registerMacro()
    {
        Builder::mixin(new QueryBuilderMixin());
    }
}
