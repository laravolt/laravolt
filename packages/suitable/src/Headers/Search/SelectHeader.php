<?php

namespace Laravolt\Suitable\Headers\Search;

use Laravolt\Suitable\Concerns\HtmlHelper;

class SelectHeader implements \Laravolt\Suitable\Contracts\Header
{
    use HtmlHelper;

    protected $name;

    protected $options = [];

    protected $attributes = [];

    public function __construct($name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    public static function make($name, array $options)
    {
        return new self($name, $options);
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
            '<th %s>%s</th>',
            $this->tagAttributes($this->attributes),
            form()->select("_s[$this->name]", $this->options, request("_s.".$this->name))
                ->attribute('form', 'suitable-form-searchable')
        );
    }
}
