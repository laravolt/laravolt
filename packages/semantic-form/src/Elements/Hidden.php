<?php

namespace Laravolt\SemanticForm\Elements;

class Hidden extends Input
{
    protected $attributes = [
        'type' => 'hidden',
    ];

    public function render(): string
    {
        $this->beforeRender();

        $result = '<input';
        $result .= $this->renderAttributes();
        $result .= '>';

        return $result;
    }
}
