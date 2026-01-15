<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class DateTime extends Element
{
    protected $attributes = [
        'type' => 'datetime-local',
        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500',
    ];

    public function __construct($name)
    {
        $this->setName($name);
    }

    public function render()
    {
        return '<input'.$this->renderAttributes().'>';
    }
}
