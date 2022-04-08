<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Tables;

use Illuminate\Database\Eloquent\Model;
use Laravolt\AutoCrud\SchemaTransformer;
use Laravolt\Fields\Field;
use Laravolt\Suitable\Columns\BelongsTo;
use Laravolt\Suitable\Columns\Button;
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
        $searchableFields = $this->fields
            ->reject(fn ($item) => $item['type'] === Field::BELONGS_TO && ! isset($item['searchable']))
            ->reject(function ($item) {
                return ($item['searchable'] ?? true) === false;
            })
            ->transform(function ($item) {
                if ($item['type'] === Field::BELONGS_TO) {
                    $item['name'] .= '.'.$item['searchable'];
                }

                return $item;
            })
            ->pluck('name')
            ->toArray();

        return $model->newQuery()
            ->whereLike($searchableFields, $this->search)
            ->autoSort($this->sortPayload())
            ->paginate();
    }

    public function columns(): array
    {
        $columns = [];
        foreach ($this->fields as $field) {
            switch ($field['type']) {
                case Field::BELONGS_TO:
                    $column = BelongsTo::make($field['name'], $field['label'] ?? '-');
                    if (isset($field['sortable'])) {
                        $column->sortable($field['name'].'.'.$field['sortable']);
                    }

                    break;
                case Field::BUTTON:
                    $column = Button::make('', $field['label'] ?? '-')
                        ->label($field['label'] ?? 'Button')
                        ->icon($field['icon'] ?? '');
                    break;
                default:
                    $column = Raw::make($field['name'], $field['label'] ?? '-');
                    if ($field['sortable'] ?? true) {
                        $column->sortable($field['name']);
                    }
            }
            $columns[] = $column;
        }

        $columns[] = $this->restfulButton();

        return $columns;
    }

    protected function restfulButton()
    {
        return RestfulButton::make('auto-crud::resource')->routeParameters(['resource' => $this->resource['key']]);
    }
}
