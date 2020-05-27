<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CardFooterComponent extends Component
{
    /**
     * @var string
     */
    public $left;

    /**
     * @var string
     */
    public $right;

    /**
     * CardComponent constructor.
     *
     * @param string $left
     * @param string $right
     */
    public function __construct(string $left = null, string $right = null)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function render()
    {
        return view('laravolt::components.card-footer');
    }
}
