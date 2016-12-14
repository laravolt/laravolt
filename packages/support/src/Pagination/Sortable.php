<?php
namespace Laravolt\Support\Pagination;

class Sortable
{
    protected static $mapping = [
        'asc'  => 'ascending sorted',
        'desc' => 'descending sorted',
    ];

    public static function link($parameters)
    {
        if (count($parameters) == 1) {
            $parameters[1] = ucfirst($parameters[0]);
        }

        $column = $parameters[0];
        $title = $parameters[1];
        $attributes = array_get($parameters, 2, []);

        $class = '';
        if (request('orderBy') == $column && in_array(request('sortedBy'), ['asc', 'desc'])) {
            $class = static::$mapping[request('sortedBy')];
        }

        $queryString = [
            'orderBy'  => $column,
            'sortedBy' => request('sortedBy') === 'asc' ? 'desc' : 'asc',
        ];

        $url = route(request()->route()->getName(), array_merge(request()->input(), $queryString));

        $attributes = static::renderAttributes($attributes);

        return '<th class="selectable '.$class.'" '.$attributes.'><a href="'.$url.'"'.'>'.htmlentities($title).'</a></th>';
    }

    protected static function renderAttributes($attributes)
    {
        $result = '';

        foreach ($attributes as $attribute => $value) {
            $result .= " {$attribute}=\"{$value}\"";
        }

        return $result;
    }
}
