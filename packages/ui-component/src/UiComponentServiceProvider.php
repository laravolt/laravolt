<?php

namespace Laravolt\UiComponent;

use Illuminate\Support\ServiceProvider;
use Laravolt\UiComponent\Livewire\UserTable;
use Livewire\Livewire;

class UiComponentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('laravolt.table.builder', fn () => new TableBuilder());
    }

    public function boot()
    {
        Livewire::component('laravolt::user-table', UserTable::class);
    }
}
