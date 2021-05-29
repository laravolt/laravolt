<?php

namespace Laravolt\Workflow\Livewire;

use Illuminate\Support\Str;
use Laravolt\Suitable\Columns\Button;
use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\MultipleValues;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Laravolt\Workflow\Models\ProcessInstance;

class ProcessInstancesTable extends TableView
{
    public array $variables = [];

    public function data()
    {
        $query = ProcessInstance::query()
            ->whereHas('definition')
            ->autoSort($this->sortPayload())
            ->latest();

        if ($this->search) {
            $searchableColumns = [];
            foreach ($this->variables as $var) {
                $searchableColumns[] = "variables->{$var}->value";
            }

            $query->whereLike(
                $searchableColumns,
                $this->search
            );
        }

        return $query;
    }

    public function columns(): array
    {
        $columns = [
            Numbering::make('No'),
        ];

        if (empty($this->variables)) {
            $columns[] = Text::make('process_instance_id', 'ID');
        }

        foreach ($this->variables as $var) {
            $columns[] = Raw::make(fn ($item) => $item->variables->getValue($var, '-'), Str::title($var));
        }

        $columns[] = MultipleValues::make('tasks', 'Tasks');
        $columns[] = Date::make('created_at', 'Created At');
        $columns[] = Button::make(
            fn ($item) => route('workflow::module.instances.show', ['rekrutmen', $item->id]),
            'Action'
        )->icon('eye');

        return $columns;
    }

    public function filters(): array
    {
        return [
            // TODO: define your filters here
        ];
    }
}
