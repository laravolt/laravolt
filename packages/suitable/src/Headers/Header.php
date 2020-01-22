<?php

namespace Laravolt\Suitable\Headers;

use Laravolt\Suitable\Concerns\HtmlHelper;

class Header implements \Laravolt\Suitable\Contracts\Header
{
    use HtmlHelper;

    protected $content;

    protected $attributes = [];

    public function __construct($content)
    {
        $this->content = $content;
    }

    public static function make($content)
    {
        return new self($content);
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
        return sprintf('<th %s>%s</th>', $this->tagAttributes($this->attributes), $this->content);
    }
}
