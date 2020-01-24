<?php

namespace Laravolt\Workflow\TableView;

use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\Headers\Search\SelectHeader;
use Laravolt\Suitable\TableView;
use Laravolt\Workflow\Models\CamundaForm;

class ManagementCamundaTableView extends TableView
{
    protected $search = true;

    protected $title = 'Form Fields';

    protected function columns()
    {
        $processes = CamundaForm::distinct('process_definition_key')
            ->pluck('process_definition_key', 'process_definition_key')
            ->prepend('--Semua--', '0')
            ->toArray();

        $taskNames = CamundaForm::distinct('task_name')
            ->when(request('filter.process_definition_key'), function ($query, $val) {
                $query->where('process_definition_key', $val);
            })
            ->pluck('task_name', 'task_name')
            ->prepend('--Semua--', '0')
            ->toArray();

        return [
            Numbering::make('No'),
            Text::make('process_definition_key', 'Process Def')
                ->searchable(null, SelectHeader::make('process_definition_key', $processes)),
            Text::make('task_name', 'Task Name')
                ->sortable()
                ->searchable(null, SelectHeader::make('task_name', $taskNames)),
            Text::make('form_name', 'Form Name')->searchable(),
            Text::make('field_name', 'Field Name')->searchable(),
            Text::make('field_type', 'Type')->searchable(),
            Text::make('field_label', 'Label')->searchable(),
            RestfulButton::make('managementcamunda')->except('view'),
        ];
    }
}
