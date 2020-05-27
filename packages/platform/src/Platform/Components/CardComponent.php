<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CardComponent extends Component
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $url;

    /**
     * CardComponent constructor.
     */
    public function __construct(string $title = null, string $content = null, string $url = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->url = $url;
    }

    public function render()
    {
        return view('laravolt::components.card');
    }
}
