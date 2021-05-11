<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Laravolt\Workflow\FieldFormatter\CamundaFormatter;
use Spatie\DataTransferObject\DataTransferObject;

class Form extends DataTransferObject
{
    public array $schema;

    public array | null $data;

    protected $callbacks = [];

    public function toCamundaVariables(): array
    {
        $variables = CamundaFormatter::format($this->data, $this->schema);
        foreach ($this->callbacks as $callback) {
            $variables = call_user_func($callback, $variables);
        }

        return $variables;
    }

    public function modifyVariables(\Closure $callback): void
    {
        $this->callbacks[] = $callback;
    }
}
