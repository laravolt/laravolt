<?php

namespace Laravolt\Workflow\Livewire;

use Illuminate\Support\Str;
use Laravolt\Suitable\Columns\Button;
use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\MultipleValues;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RowNumber;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class ProcessInstancesTable extends TableView
{
    protected Module $module;

    public function mount(Module $module)
    {
        $this->module = $module;
    }

    public function data()
    {
        $query = ProcessInstance::query()
            // ->whereHas('definition')
            ->where('definition_key', $this->module->processDefinitionKey)
            ->autoSort($this->sortPayload())
            ->latest();

        if ($this->search) {
            $searchableColumns = [];
            foreach ($this->module->tableVariables as $var) {
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
            RowNumber::make('No'),
        ];

        if (empty($this->module->tableVariables)) {
            $columns[] = Text::make('process_instance_id', 'ID');
        }

        foreach ($this->module->tableVariables as $var) {
            $columns[] = Raw::make(fn ($item) => $item->variables->getValue($var, '-'), Str::title($var));
        }

        $columns[] = MultipleValues::make('tasks', 'Tasks');
        $columns[] = Date::make('created_at', 'Created At');
        $columns[] = Button::make(
            fn ($item) => route('workflow::module.instances.show', [request()->route('module'), $item->id]),
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
