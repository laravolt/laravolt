<?php

namespace Laravolt\Ui\Filters;

class TextFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();
        $field = form()->text($key);

        if ($this->placeholder) {
            $field->placeholder($this->placeholder);
        }

        return $field
            ->label($this->label)
            ->attributes(['wire:model.debounce.300ms' => "filters.$key"]);
    }
}
