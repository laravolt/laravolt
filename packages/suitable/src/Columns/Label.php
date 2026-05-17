<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Label extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'center aligned'];

    protected $labelClass = [];

    protected $labelClassIf = [];

    public function cell($cell, $collection, $loop)
    {
        $label = data_get($cell, $this->field);

        if ($label !== null) {
            $class = implode(' ', $this->labelClass);

            foreach (($this->labelClassIf[$label] ?? []) as $additionalClass) {
                $class .= " $additionalClass";
            }

            $base = 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium';
            $defaultPalette = 'bg-gray-100 text-gray-800 dark:bg-white/10 dark:text-white';

            // If the caller passed no palette via addClassIf()/map(), give a sensible
            // neutral Preline badge palette so v7 admin tables don't render bare text.
            $palette = trim($class) !== '' ? $class : $defaultPalette;

            return sprintf('<span class="%s %s">%s</span>', $base, $palette, $label);
        }

        return '-';
    }

    public function addClass(string $class)
    {
        $this->labelClass[] = $class;

        return $this;
    }

    public function addClassIf($value, string $class)
    {
        $this->labelClassIf[$value][] = $class;

        return $this;
    }

    public function map(array $map)
    {
        foreach ($map as $value => $class) {
            $this->addClassIf($value, $class);
        }

        return $this;
    }
}
