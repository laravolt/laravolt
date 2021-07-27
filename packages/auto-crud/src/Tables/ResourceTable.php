<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Tables;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Fields\Field;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Ui\TableView;

class ResourceTable extends TableView
{
    public array $resource;

    protected $fields;

    public function data()
    {
        $this->fields = collect($this->resource['schema'])
            ->transform(function ($item) {
                if ($item instanceof Field) {
                    $item = $item->toArray();
                }
                return $item;
            })
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
            if ($field['type'] === Field::BELONGS_TO) {
                $column = function ($item) use ($field) {
                    return call_user_func($field['display'], $item);
                };
            } else {
                $column = $field['name'];
            }
            $columns[] = Raw::make($column, $field['label'] ?? '-');
        }

        $columns[] = RestfulButton::make('auto-crud::resource')
            ->routeParameters(['resource' => $this->resource['key']]);

        return $columns;
    }
}
