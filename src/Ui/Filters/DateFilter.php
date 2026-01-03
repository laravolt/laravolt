<?php

namespace Laravolt\Ui\Filters;

class DateFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();
        $field = form()->datepicker($key);

        if ($this->placeholder) {
            $field->placeholder($this->placeholder);
        }

        return $field
            ->label($this->label)
            ->attributes(['wire:model.live' => "filters.$key"]);
    }
}
