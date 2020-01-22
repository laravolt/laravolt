<?php

namespace Laravolt\Workflow\TableView;

use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class SegmentTableView extends TableView
{
    protected function columns()
    {
        return [
            Numbering::make('No')->setCellAttributes([
                'style' => 'background-color:white',
                'class' => 'ui center aligned',
            ]),
            Text::make('process_definition_key', 'Process Def'),
            Text::make('task_name', 'Task Name')->sortable(),
            Text::make('segment_name', 'Segment Name')->sortable(),
            Text::make('segment_order', 'Segment Order'),
            RestfulButton::make('segment'),
        ];
    }
}
