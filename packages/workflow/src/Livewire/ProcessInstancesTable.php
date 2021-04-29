<?php

namespace Laravolt\Workflow\Livewire;

use Laravolt\Suitable\Columns\Button;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Laravolt\Workflow\Models\ProcessInstance;

class ProcessInstancesTable extends TableView
{
    protected bool $showSearchbox = false;

    public function data()
    {
        $query = ProcessInstance::query()
            ->autoSort($this->sortPayload())
            ->latest();

        return $query;
    }

    public function columns(): array
    {
        return [
            Text::make('id', 'ID')->sortable(),
            Raw::make(fn ($item) => $item->variables->get('full_name')->value, 'Nama'),
            Raw::make(function ($item) {
                return $item->tasks->pluck('taskDefinitionKey');
            }),
            Button::make('permalink', 'Action')->icon('eye'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }
}
