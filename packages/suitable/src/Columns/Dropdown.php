<?php

namespace Laravolt\Suitable\Columns;

class Dropdown extends Column implements ColumnInterface
{
    protected $options = [];

    protected $cssClass = [];

    public static function make($field = null, $header = null)
    {
        return parent::make($field, '');
    }

    public function item(string $label, string $url, string $icon = null): self
    {
        $this->options[] = compact('label', 'url', 'icon');

        return $this;
    }

    public function divider(): self
    {
        $this->options[] = '-';

        return $this;
    }

    public function addClass(string $class): self
    {
        $this->cssClass[] = $class;

        return $this;
    }

    public function cell($cell, $collection, $loop)
    {
        $color = config('laravolt.ui.color');

        $this->options = [];
        call_user_func($this->field, $this, $cell);

        $menu = collect($this->options)->map(function ($item) {
            if ($item === '-') {
                return '<div class="divider"></div>';
            } else {
                return "<a class='item' href='{$item['url']}'><i class='icon {$item['icon']}'></i> {$item['label']}</a>";
            }
        })->implode('');

        $cssClass = collect($this->cssClass)->unique()->implode(' ');

        return <<<HTML
        <div class="ui icon simple dropdown secondary $color button $cssClass">
          <i class="ellipsis horizontal icon"></i>
          <div class="menu inverted">
            $menu
          </div>
        </div>            
        HTML;
    }
}
