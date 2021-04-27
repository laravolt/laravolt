<?php

namespace Laravolt\Workflow\Livewire;

use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Laravolt\Workflow\Models\ProcessDefinition;

class DefinitionTable extends TableView
{
    public function data()
    {
        $sortPayload = [
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];

        $query = ProcessDefinition::query()->autoSort('sort', 'direction', $sortPayload)->latest();

        $keyword = trim($this->search);
        if ($keyword !== '') {
            $query->whereLike(['name', 'key'], $keyword);
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Text::make('name', 'Name'),
            Text::make('key', 'Key'),
            Text::make('version', 'Version'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }
}
