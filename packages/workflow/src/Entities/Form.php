<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Illuminate\Support\Facades\Validator;
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

    public function modifyVariables(\Closure $callback): void
    {
        $this->callbacks[] = $callback;
    }

    public function rules(): array
    {
        return collect($this->schema)
            ->transform(fn ($item) => $item['rules'] ?? [])
            ->toArray();
    }

    public function validate(): array
    {
        $validator = Validator::make($this->data, $this->rules());

        return $validator->validate();
    }
}
