<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Link extends Element
{
    protected $url;

    protected $text;

    public function __construct($text, $url)
    {
        $this->url = $url;
        $this->text = $text;
        $this->setAttribute('href', $url);
        $this->addClass('text-blue-600 decoration-2 hover:underline font-medium dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');
    }

    public function render()
    {
        return sprintf('<a%s>%s</a>', $this->renderAttributes(), form_escape($this->text));
    }
}
