<?php

namespace Laravolt\PrelineForm\Elements;

use Laravolt\PrelineForm\FieldCollection;

class FieldLayout extends Element
{
    protected FieldCollection $fields;

    protected array $items;

    protected string $type;

    protected int $columns;

    protected static array $columnClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        5 => 'grid-cols-5',
        6 => 'grid-cols-6',
        7 => 'grid-cols-7',
        8 => 'grid-cols-8',
        9 => 'grid-cols-9',
        10 => 'grid-cols-10',
        11 => 'grid-cols-11',
        12 => 'grid-cols-12',
    ];

    protected static array $spanClasses = [
        1 => 'col-span-1',
        2 => 'col-span-2',
        3 => 'col-span-3',
        4 => 'col-span-4',
        5 => 'col-span-5',
        6 => 'col-span-6',
        7 => 'col-span-7',
        8 => 'col-span-8',
        9 => 'col-span-9',
        10 => 'col-span-10',
        11 => 'col-span-11',
        12 => 'col-span-12',
    ];

    protected static array $gapClasses = [
        0 => 'gap-0',
        1 => 'gap-1',
        2 => 'gap-2',
        3 => 'gap-3',
        4 => 'gap-4',
        5 => 'gap-5',
        6 => 'gap-6',
        7 => 'gap-7',
        8 => 'gap-8',
        9 => 'gap-9',
        10 => 'gap-10',
        11 => 'gap-11',
        12 => 'gap-12',
    ];

    protected static array $startClasses = [
        1 => 'col-start-1',
        2 => 'col-start-2',
        3 => 'col-start-3',
        4 => 'col-start-4',
        5 => 'col-start-5',
        6 => 'col-start-6',
        7 => 'col-start-7',
        8 => 'col-start-8',
        9 => 'col-start-9',
        10 => 'col-start-10',
        11 => 'col-start-11',
        12 => 'col-start-12',
        13 => 'col-start-13',
    ];

    public function __construct(string $type, FieldCollection $fields, array $options = [])
    {
        $this->type = $type;
        $this->fields = $fields;
        $this->items = array_values($options['items'] ?? []);
        $this->columns = $this->normalizeColumns($options['columns'] ?? $options['column'] ?? 1);

        $this->attributes($options['attributes'] ?? []);
        $this->addClass($this->containerClass($options));
    }

    public function render()
    {
        $output = sprintf('<div%s>', $this->renderAttributes());

        foreach (array_values($this->fields->all()) as $index => $field) {
            $output .= sprintf(
                '<div class="%s">%s</div>',
                form_escape($this->itemClass($this->items[$index] ?? [])),
                $field
            );
        }

        $output .= '</div>';

        return $output;
    }

    public function bindValues(array $values)
    {
        $this->fields->bindValues($values);

        return $this;
    }

    protected function containerClass(array $options): string
    {
        $classes = ['grid', static::$columnClasses[$this->columns], $this->gapClass($options['gap'] ?? 4)];

        if (isset($options['classes'])) {
            $classes[] = $options['classes'];
        }

        return trim(implode(' ', array_filter($classes)));
    }

    protected function itemClass($item): string
    {
        if (! is_array($item)) {
            return 'col-span-1';
        }

        return trim(implode(' ', array_filter([
            $this->spanClass($item['colSpan'] ?? $item['columnSpan'] ?? $item['width'] ?? null),
            $this->startClass($item['colStart'] ?? $item['columnStart'] ?? $item['start'] ?? null),
            $item['layoutClass'] ?? $item['wrapperClass'] ?? null,
        ])));
    }

    protected function spanClass(mixed $span): string
    {
        if ($span === null) {
            return 'col-span-1';
        }

        if ($span === 'full') {
            return 'col-span-full';
        }

        if (is_string($span) && str_contains($span, '/')) {
            return $this->fractionalSpanClass($span);
        }

        $span = (int) $span;
        $span = max(1, min(12, $span));

        return static::$spanClasses[$span];
    }

    protected function startClass(mixed $start): ?string
    {
        if ($start === null) {
            return null;
        }

        $start = (int) $start;
        $start = max(1, min(13, $start));

        return static::$startClasses[$start];
    }

    protected function fractionalSpanClass(string $span): string
    {
        [$numerator, $denominator] = array_pad(explode('/', $span, 2), 2, 1);
        $numerator = (int) $numerator;
        $denominator = (int) $denominator;

        if ($numerator <= 0 || $denominator <= 0) {
            return 'col-span-1';
        }

        $columns = max(1, (int) round($this->columns * ($numerator / $denominator)));
        $columns = min(12, $columns);

        return static::$spanClasses[$columns];
    }

    protected function gapClass(int|string $gap): string
    {
        if (is_numeric($gap)) {
            $gap = max(0, min(12, (int) $gap));

            return static::$gapClasses[$gap];
        }

        if (in_array($gap, static::$gapClasses, true)) {
            return $gap;
        }

        return static::$gapClasses[4];
    }

    protected function normalizeColumns(int|string $columns): int
    {
        $columns = (int) $columns;

        return max(1, min(12, $columns));
    }
}
