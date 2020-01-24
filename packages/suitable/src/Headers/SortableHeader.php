<?php

namespace Laravolt\Suitable\Headers;

use Laravolt\Suitable\Concerns\HtmlHelper;
use Laravolt\Suitable\Contracts\Header;

class SortableHeader implements \Laravolt\Suitable\Contracts\Header
{
    use HtmlHelper;

    protected static $classMapping = [
        'asc'  => [
            'header' => 'ascending sorted',
            'icon'   => 'caret up',
        ],
        'desc' => [
            'header' => 'descending sorted',
            'icon'   => 'caret down',
        ],
    ];

    protected $attributes;

    public function __construct($title, $column)
    {
        $this->title = $title;
        $this->column = $column;
    }

    public static function make($title, $column)
    {
        return new self($title, $column);
    }

    public function setAttributes(array $attributes): Header
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function render(): string
    {
        $sortByKey = config('suitable.query_string.sort_by');
        $sortDirectionKey = config('suitable.query_string.sort_direction');

        $headerClass = '';
        $iconClass = 'sort';
        if (request($sortByKey) == $this->column && in_array(request($sortDirectionKey), ['asc', 'desc'])) {
            $headerClass = self::$classMapping[request($sortDirectionKey)]['header'];
            $iconClass = self::$classMapping[request($sortDirectionKey)]['icon'];
        }

        $icon = sprintf('<i class="icon %s"></i>', $iconClass);

        $sortableQueryString = [
            $sortByKey        => $this->column,
            $sortDirectionKey => request($sortDirectionKey) === 'asc' ? 'desc' : 'asc',
        ];

        $queryString = array_merge(request()->input(), $sortableQueryString);

        $url = request()->url().'?'.http_build_query($queryString);

        $this->attributes['class'] = ($this->attributes['class'] ?? '').' '.$headerClass;
        $attributes = $this->tagAttributes($this->attributes);

        return sprintf('<th %s><a href="%s">%s %s</a></th>', $attributes, $url, htmlentities($this->title), $icon);
    }
}
