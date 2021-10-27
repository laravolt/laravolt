<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Tables;

use Illuminate\Database\Eloquent\Model;
use Laravolt\AutoCrud\SchemaTransformer;
use Laravolt\Fields\Field;
use Laravolt\Suitable\Columns\BelongsTo;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Ui\TableView;

class ResourceTable extends TableView
{
    public array $resource;

    protected $fields;

    public function data()
    {
        $transformer = new SchemaTransformer($this->resource);

        $this->fields = $transformer->getFieldsForIndex();

        /** @var Model $model */
        $model = app($this->resource['model']);
        $searchableFields = $this->fields->reject(fn($item) => $item['type'] === Field::BELONGS_TO)->pluck('name')->toArray();

        return $model->newQuery()->whereLike($searchableFields, $this->search)->autoSort($this->sortPayload())->paginate();
    }

    public function columns(): array
    {
        $columns = [];
        foreach ($this->fields as $field) {
            if ($field['type'] === Field::BELONGS_TO) {
                $column = BelongsTo::make($field['name'], $field['label'] ?? '-');
                if (isset($field['sort_by'])) {
                    $column->sortable($field['name'].'.'.$field['sort_by']);
                }
                $columns[] = $column;
            } else {
                $columns[] = Raw::make($field['name'], $field['label'] ?? '-')->sortable($field['name']);
            }

        }

        $columns[] = RestfulButton::make('auto-crud::resource')
            ->routeParameters(['resource' => $this->resource['key']]);

        return $columns;
    }
}
