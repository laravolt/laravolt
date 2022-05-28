<?php

namespace Laravolt\AutoCrud;

use Illuminate\Support\Collection;
use Laravolt\Fields\Field;

class SchemaTransformer
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function transform()
    {
        return collect($this->config['schema'])
            ->transform(function ($item) {
                if ($item['type'] === Field::BELONGS_TO) {
                    /** @var \Illuminate\Database\Eloquent\Model $model */
                    $model = app($this->config['model']);

                    /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relationship */
                    $relationship = $model->{$item['name']}();

                    /** @var \Illuminate\Database\Eloquent\Model $relatedModel */
                    $relatedModel = $relationship->getRelated();

                    $hasDisplayMethod = method_exists($relatedModel, 'display');

                    $item['type'] = Field::DROPDOWN;
                    $item['options'] =
                        $relatedModel::query()->get()->mapWithKeys(function ($model) use ($hasDisplayMethod) {
                            $label = $hasDisplayMethod ? $model->display() : (string) $model;

                            return [$model->getKey() => $label];
                        });
                    $item['name'] = $relationship->getForeignKeyName();
                }

                return $item;
            })
            ->toArray();
    }

    public function getFieldsForCreate(): Collection
    {
        return collect($this->transform())->filter(fn ($item) => $item['show_on_create'] ?? true);
    }

    public function getFieldsForEdit(): Collection
    {
        return collect($this->transform())->filter(fn ($item) => $item['show_on_edit'] ?? true);
    }

    public function getFieldsForDetail(): Collection
    {
        return collect($this->transform())->filter(fn ($item) => $item['show_on_detail'] ?? true);
    }

    public function getFieldsForIndex(): Collection
    {
        return collect($this->config['schema'])->filter(fn ($item) => $item['show_on_index'] ?? true);
    }
}
