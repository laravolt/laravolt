<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Laravolt\Fields\Field;
use Laravolt\Workflow\FieldFormatter\CamundaFormatter;
use Spatie\DataTransferObject\DataTransferObject;

class Form extends DataTransferObject
{
    public array $schema;

    public array $data;

    protected array $callbacks = [];

    public function toCamundaVariables(): array
    {
        $variables = CamundaFormatter::format($this->data, $this->schema);
        foreach ($this->callbacks as $callback) {
            $variables = $callback($variables);
        }

        return $variables;
    }

    public function modifyVariables(Closure $callback): void
    {
        $this->callbacks[] = $callback;
    }

    public function rules(): array
    {
        return collect($this->schema)
            ->mapWithKeys(
                function ($item, $key) {
                    if ($item instanceof Field) {
                        $item = $item->toArray();
                    }
                    if (Arr::get($item, 'type') === 'uploader' && Arr::get($this->data, '_'.$key) !== '[]') {
                        $key = '_'.$key;
                    }

                    return [$key => $item['rules'] ?? []];
                }
            )
            ->toArray();
    }

    public function validate(): array
    {
        $validator = Validator::make($this->data, $this->rules());

        return $validator->validate();
    }
}
