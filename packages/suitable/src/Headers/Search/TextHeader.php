<?php

namespace Laravolt\Suitable\Headers\Search;

use Illuminate\Support\Arr;
use Laravolt\Suitable\Concerns\HtmlHelper;

class TextHeader implements \Laravolt\Suitable\Contracts\Header
{
    use HtmlHelper;

    protected $name;

    protected $attributes = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public static function make($name)
    {
        return new self($name);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function setAttributes(array $attributes): \Laravolt\Suitable\Contracts\Header
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function render(): string
    {
        return sprintf(
            '<th %s><input type="text" name="filter[%s]" value="%s" form="suitable-form-searchable"></th>',
            $this->tagAttributes($this->attributes),
            $this->name,
            Arr::get(request('filter'), $this->name)
        );
    }
}
