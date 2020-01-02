<?php

declare(strict_types=1);

namespace Laravolt\Workflow\TableView;

use Laravolt\Suitable\Columns\Dummy;
use Laravolt\Suitable\Columns\Id;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class ModuleTableView extends TableView
{
    protected $search = false;

    protected $title = 'All Modules';

    protected function columns()
    {
        return [
            Numbering::make('No'),
            Id::make('id'),
            Text::make('label', 'Modul'),
            Text::make('process_definition_key'),
            Dummy::make(''),
        ];
    }
}
