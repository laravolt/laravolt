<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class LinkButton extends Element
{
    protected $url;

    protected $text;

    public function __construct($text, $url)
    {
        $this->url = $url;
        $this->text = $text;
        $this->setAttribute('href', $url);
        $this->addClass('py-2 px-3 inline-flex justify-center items-center text-start bg-white border border-gray-200 text-gray-800 text-sm font-medium rounded-lg shadow-2xs align-middle hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700');
    }

    public function render()
    {
        return sprintf('<a%s>%s</a>', $this->renderAttributes(), form_escape($this->text));
    }
}
