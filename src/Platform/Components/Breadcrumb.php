<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public function __construct()
    {
        ComponentManager::$currentComponent = self::class;
    }

    public function render()
    {
        return view('laravolt::components.breadcrumb');
    }
}
