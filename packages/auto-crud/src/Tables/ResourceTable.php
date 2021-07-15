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

    protected $fields;

    public function data()
    {
        $this->fields = collect($this->resource['schema'])
            ->filter(
                function ($item) {
                    return ($item['visibility']['index'] ?? true);
                }
            );

        /** @var Model $model */
        $model = app($this->resource['model']);

        return $model->newQuery()->whereLike($this->fields->pluck('name')->toArray(), $this->search)->paginate();
    }

    public function columns(): array
    {
        $columns = [];
        foreach ($this->fields as $field) {
            $columns[] = Raw::make($field['name']);
        }

        $columns[] = RestfulButton::make('auto-crud::resource')
            ->routeParameters(['resource' => $this->resource['key']]);

        return $columns;
    }
}
