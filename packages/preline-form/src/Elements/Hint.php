<?php

namespace Laravolt\PrelineForm\Elements;

class Hint extends Element
{
    protected $text;
    public static $defaultClass = 'text-sm text-gray-500 dark:text-gray-400 mt-1';

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