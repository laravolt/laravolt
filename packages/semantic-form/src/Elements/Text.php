<?php

namespace Laravolt\SemanticForm\Elements;

class Text extends Input
{
    protected $attributes = [
        'type' => 'text',
        'class' => 'block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300',
    ];

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);

        return $this;
    }

    public function defaultValue($value)
    {
        if (! $this->hasValue()) {
            $this->setValue($value);
        }

        return $this;
    }

    protected function hasValue()
    {
        return isset($this->attributes['value']);
    }
}
