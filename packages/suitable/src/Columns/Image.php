<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Image extends Column implements ColumnInterface
{
    protected int $width;

    protected int $height;

    protected string $alt = '';

    public function cell($cell, $collection, $loop)
    {
        return sprintf(
            '<img width="%s" height="%s" class="ui image" src="%s"  alt="%s"/>',
            $this->width ?? '',
            $this->height ?? '',
            data_get($cell, $this->field),
            $this->alt,
        );
    }

    public function width(int $width)
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height)
    {
        $this->height = $height;

        return $this;
    }

    public function alt(string $text)
    {
        $this->alt = $text;

        return $this;
    }
}
