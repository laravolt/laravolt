<?php

namespace Laravolt\AutoCrud;

use Illuminate\Support\Collection;
use Laravolt\Fields\Field;

class SchemaTransformer
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $this->processConfig($config);
    }

    /**
     * Get the processed configuration with serializable rules.
     */
    public function getProcessedConfig(): array
    {
        return $this->config;
    }

    /**
     * Process configuration to ensure all Rule objects are serializable.
     */
    protected function processConfig(array $config): array
    {
        if (isset($config['schema']) && is_array($config['schema'])) {
            foreach ($config['schema'] as $key => $item) {
                if (isset($item['rules']) && is_array($item['rules'])) {
                    $config['schema'][$key]['rules'] = $this->processRules($item['rules']);
                }
            }
        }

        return $config;
    }

    /**
     * Process validation rules to make them serializable.
     *
     * @param  array|mixed  $rules
     * @return array|mixed
     */
    protected function processRules($rules)
    {
        if (! is_array($rules)) {
            if ($rules instanceof \Illuminate\Validation\Rules\Unique) {
                // Convert Unique rule to string representation to make it serializable
                // We'll capture the essential properties that can be reconstructed later
                return 'unique:'.$this->getUniqueRuleTable($rules).','.$this->getUniqueRuleColumn($rules);
            }

            if (is_object($rules) && ! method_exists($rules, '__toString')) {
                // Convert other non-stringable rule objects to strings to avoid serialization issues
                return (string) $rules;
            }

            return $rules;
        }

        return array_map([$this, 'processRules'], $rules);
    }

    /**
     * Get the table name from a Unique rule.
     *
     * @param  \Illuminate\Validation\Rules\Unique  $rule
     */
    protected function getUniqueRuleTable($rule): string
    {
        try {
            $reflector = new \ReflectionClass($rule);
            $property = $reflector->getProperty('table');
            $property->setAccessible(true);

            return $property->getValue($rule);
        } catch (\Exception $e) {
            return 'unknown_table';
        }
    }

    /**
     * Get the column name from a Unique rule.
     *
     * @param  \Illuminate\Validation\Rules\Unique  $rule
     */
    protected function getUniqueRuleColumn($rule): string
    {
        try {
            $reflector = new \ReflectionClass($rule);
            $property = $reflector->getProperty('column');
            $property->setAccessible(true);

            return $property->getValue($rule) ?: 'NULL';
        } catch (\Exception $e) {
            return 'NULL';
        }
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
