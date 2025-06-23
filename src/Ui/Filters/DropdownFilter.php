<?php

namespace Laravolt\Ui\Filters;

class DropdownFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();
        $field = form()->dropdown($key, $this->options());

        if ($this->placeholder) {
            $field->placeholder($this->placeholder);
        }

        return $field
            ->label($this->label)
            ->removeClass('clearable')
            ->attributes(['wire:model' => "filters.$key"]);
    }

    public function options(): array
    {
        return [];
    }
}
