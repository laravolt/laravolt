<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Concerns;

trait HtmlHelper
{
    protected function tagAttributes(array $attributes)
    {
        $tagAttributes = '';

        foreach ($attributes as $attribute => $value) {
            $tagAttributes .= " {$attribute}=\"{$value}\"";
        }

        return $tagAttributes;
    }
}
