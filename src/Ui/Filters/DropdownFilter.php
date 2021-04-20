<?php

namespace Laravolt\Ui\Filters;

class DropdownFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();

        return form()
            ->dropdown($key, $this->options())
            ->label($this->label())
            ->removeClass('clearable')
            ->attributes(['wire:model' => "filters.$key"]);
    }

    public function options(): array
    {
        return [];
    }
}
