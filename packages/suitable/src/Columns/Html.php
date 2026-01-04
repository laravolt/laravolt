<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

use Closure;

class Html extends Column implements ColumnInterface
{
    protected $allowedTags = [
        '<a>',
        '<b>',
        '<blockquote>',
        '<br>',
        '<code>',
        '<del>',
        '<dd>',
        '<dl>',
        '<dt>',
        '<em>',
        '<h1>',
        '<h2>',
        '<h3>',
        '<h4>',
        '<h5>',
        '<h6>',
        '<hr>',
        '<i>',
        '<img>',
        '<kbd>',
        '<li>',
        '<ol>',
        '<p>',
        '<pre>',
        '<s>',
        '<sup>',
        '<sub>',
        '<strong>',
        '<strike>',
        '<ul>',
    ];

    public function cell($cell, $collection, $loop)
    {
        if ($this->field instanceof Closure) {
            $html = call_user_func($this->field, $cell, $collection, $loop);
        } else {
            $html = data_get($cell, $this->field);
        }

        $allowedTags = implode($this->allowedTags);

        return strip_tags($html, $allowedTags);
    }

    public function setAllowedTags(array $allowedTags)
    {
        $this->allowedTags = $allowedTags;

        return $this;
    }
}
