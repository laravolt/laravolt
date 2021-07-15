<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Tables;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

class ResourceTable extends TableView
{
    public array $resource;

    public function data()
    {
        /** @var Model $model */
        $model = app($this->resource['model']);

        return $model->newQuery()->paginate();
    }

    public function columns(): array
    {
        $columns = [];
        foreach ($this->resource['schema'] as $field) {
            $columns[] = Raw::make($field['name']);
        }

        $columns[] = RestfulButton::make('auto-crud::resource')
            ->routeParameters(['resource' => $this->resource['key']])
            ->only('edit', 'destroy');

        return $columns;
    }
}
