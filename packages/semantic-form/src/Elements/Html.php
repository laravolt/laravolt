<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Html extends Element
{
    protected $content;

    /**
     * Html constructor.
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function name()
    {
    }

    public function render()
    {
        return $this->content;
    }

    public function display()
    {
        return sprintf(
            '<tr><td></td><td colspan="2">%s</td></tr>',
            $this->content
        );
    }
}
