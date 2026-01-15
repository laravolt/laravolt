<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Hint extends Element
{
    public static $defaultClass = 'mt-2 text-sm text-gray-500 dark:text-neutral-200';

    protected $text;

    public function __construct($text, $class = null)
    {
        $this->text = $text;
        $this->addClass($class ?: static::$defaultClass);
    }

    public function render()
    {
        return sprintf('<p%s>%s</p>', $this->renderAttributes(), form_escape($this->text));
    }
}
