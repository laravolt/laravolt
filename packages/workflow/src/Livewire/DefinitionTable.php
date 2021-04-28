<?php

namespace Laravolt\Workflow\Livewire;

use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Laravolt\Workflow\Models\ProcessDefinition;

class DefinitionTable extends TableView
{
    public function data()
    {
        $query = ProcessDefinition::query()
            ->autoSort($this->sortPayload())
            ->whereLike(['name', 'key'], trim($this->search))
            ->latest();

        return $query;
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Raw::make(
                function ($item) {
                    return sprintf(
                        '<a themed href="%s">%s</a>',
                        route('workflow::definitions.show', $item->key),
                        $item->key
                    );
                },
                'Key'
            ),
            Text::make('name', 'Name')->sortable(),
            Text::make('version', 'Version')->sortable(),
            RestfulButton::make('workflow::definitions', '')->only('delete'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }
}
