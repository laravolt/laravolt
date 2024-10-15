<?php

namespace Laravolt\SemanticForm\Traits;

use Illuminate\Support\Stringable;

trait Themeable
{
    public array $themeAvailableColors = [];

    public string $themeSelectedColor = '';

    protected function applyTheme()
    {
        $colors = collect($this->themeAvailableColors)->keys();
        $types = (new Stringable($this->attributes['class']))->explode(' ');

        if ($types->intersect($colors)->isEmpty()) {
            $this->addClass($this->themeSelectedColor);
        }
    }
}
