<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CardFooter extends Component
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
     */
    public function __construct(?string $left = null, ?string $right = null)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function render()
    {
        return view('laravolt::components.card-footer');
    }
}
