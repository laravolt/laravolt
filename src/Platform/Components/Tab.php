<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Tab extends Component
{
    protected static $activeTab;

    public $key;

    public static function getActiveTab()
    {
        return static::$activeTab;
    }

    public function __construct()
    {
        $this->key = 'tab-'.bin2hex(random_bytes(5));
        static::$activeTab = $this->key;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.tab');
    }
}
