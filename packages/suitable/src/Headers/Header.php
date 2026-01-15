<?php

declare(strict_types=1);

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

    public function __toString()
    {
        return $this->render();
    }

    public static function make($content)
    {
        return new self($content);
    }

    public function setAttributes(array $attributes): \Laravolt\Suitable\Contracts\Header
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributesString(): string
    {
        return $this->tagAttributes($this->attributes);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function render(): string
    {
        return sprintf('<th %s>%s</th>', $this->getAttributesString(), $this->content);
    }
}
