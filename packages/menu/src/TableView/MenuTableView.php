<?php

declare(strict_types=1);

namespace Laravolt\Menu\TableView;

use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class MenuTableView extends TableView
{
    protected function columns()
    {
        return [
            Text::make('label_prefixed', 'Label'),
            Text::make('url'),
            Raw::make(function ($item) {
                return sprintf('<i class="icon %s %s"></i>', $item->icon, $item->color);
            }, 'Icon'),
            Text::make('order'),
            Text::make('type'),
            RestfulButton::make('menu::menu')->except(['view']),
        ];
    }
}
