<?php

namespace Laravolt\UiComponent\Filters;

use Laravolt\SemanticForm\Elements\Checkbox;

class CheckboxFilter extends BaseFilter
{
    public function render(): string
    {
        $key = $this->key();

        return form()
            ->checkboxGroup($key, $this->options())
            ->label($this->label())
            ->removeClass('clearable')
            ->attributes(function (Checkbox $control) use ($key) {
                $value = $control->getValue();
                $control->attribute('wire:model', "filters.$key.$value");
            });
    }

    public function options(): array
    {
        return [];
    }
}
