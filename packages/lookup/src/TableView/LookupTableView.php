<?php

declare(strict_types=1);

namespace Laravolt\Lookup\TableView;

use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class LookupTableView extends TableView
{
    protected $config = [];

    public function config($config)
    {
        $this->config = $config;

        return $this;
    }

    protected function columns()
    {
        $columns = [
            Numbering::make('No'),
        ];

        $columns[] = Text::make('lookup_key', 'Key');
        $columns[] = Text::make('lookup_value', 'Value');

        if ($this->config['parent'] ?? false) {
            $heading = config(sprintf('laravolt.lookup.collections.%s.label', $this->config['parent']));
            $columns[] = Text::make('parent.lookup_value', $heading);
        }

        if ($this->config['description'] ?? false) {
            $columns[] = Text::make('description', 'Deskripsi');
        }

        $columns[] = RestfulButton::make('lookup::lookup')->except(['view', 'delete']);

        return $columns;
    }
}
