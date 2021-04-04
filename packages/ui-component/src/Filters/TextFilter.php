<?php

namespace Laravolt\UiComponent\Filters;

class TextFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();

        return form()
            ->text($key)
            ->label($this->label())
            ->attributes(['wire:model' => "filters.$key"]);
    }
}
