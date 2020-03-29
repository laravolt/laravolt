<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Tables;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\TableView;
use Laravolt\Workflow\Entities\ViewQuery;
use Laravolt\Workflow\Traits\WorkflowColumns;

abstract class Table extends TableView implements \Laravolt\Workflow\Contracts\Table
{
    use WorkflowColumns;

    public function init()
    {
        $this->decorate(function (Builder $builder) {
        });
    }

    public function viewQuery(): ?ViewQuery
    {
        return null;
    }
}
