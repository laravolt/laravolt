<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

use Closure;

class Button extends Column implements ColumnInterface
{
    protected string $label = '';

    protected string $icon = '';

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function cell($cell, $collection, $loop)
    {
        if ($this->field instanceof Closure) {
            $url = call_user_func($this->field, $cell);
        } else {
            $url = data_get($cell, $this->field);
        }

        if ($url) {
            $url = url($url);
        }

        return sprintf(
            '<a class="ui secondary button %s %s" themed href="%s">%s %s</a>',
            $this->icon ? 'icon' : '',
            config('laravolt.ui.color'),
            $url,
            $this->icon ? "<i class='icon $this->icon'></i>" : '',
            $this->label,
        );
    }
}
