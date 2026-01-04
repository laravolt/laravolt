<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Toolbars;

class SimplePagination extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    public static function make()
    {
        return new static;
    }

    public function render()
    {
        return sprintf(
            '<a href="%s" class="ui button %s %s">%s%s</a>',
            $this->href,
            collect($this->class)->implode(' '),
            $this->icon ? 'icon' : '',
            $this->icon ? "<i class='icon {$this->icon}'></i> " : '',
            $this->label
        );
    }
}
