<?php

namespace Laravolt\UiComponent\Filters;

class DateFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();

        return form()
            ->datepicker($key)
            ->label($this->label())
            ->attributes(['wire:model' => "filters.$key"]);
    }
}
