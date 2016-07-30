<?php
namespace Laravolt\Suitable;

class Sortable
{
    protected static $classMapping = [
        'asc'   => 'ascending sorted',
        'desc'  => 'descending sorted'
    ];

    public static function link($parameters)
    {
        if (count($parameters) == 1) $parameters[1] = ucfirst($parameters[0]);

        $column = $parameters[0];
        $title = $parameters[1];

        $class = '';
        //if (request('orderBy') == $column && in_array(request('sortedBy'), ['asc', 'desc']))
        //    $class = self::$classMapping[request('sortedBy')];
        $icon = '<i class="icon sort"></i>';

        $sortableQueryString = [
            'order' => $column,
            'sort' => request('sort') === 'asc' ? 'desc' : 'asc'
        ];

        $queryString = array_merge(request()->input(), $sortableQueryString);

        $url = request()->url().'?'.http_build_query($queryString);

        return '<th class="' . $class . '"><a href="' . $url . '"' . '>' . htmlentities($title) . ' ' . $icon . '</a></th>';

    }
}
