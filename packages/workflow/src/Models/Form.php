<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'camunda_form';

    protected $casts = [
        'field_meta' => 'array',
    ];

    public function getValidationRulesAttribute()
    {
        return explode('|', $this->field_meta['validation'] ?? '');
    }

    public static function getFormName(string $processDefinitionKey, string $taskName)
    {
        return static::query()
            ->where('process_definition_key', $processDefinitionKey)
            ->where('task_name', $taskName)
            ->value('form_name');
    }

    public static function getFields(string $processDefinitionKey, string $taskName, string $formName = null)
    {
        if ($formName == null) {
            return static::query()
                ->where('process_definition_key', $processDefinitionKey)
                ->where('task_name', $taskName)
                ->orderBy('field_order')
                ->get();
        } else {
            return static::query()
                ->where('process_definition_key', $processDefinitionKey)
                ->where('task_name', $taskName)
                ->where('form_name', $formName)
                ->orderBy('field_order')
                ->get();
        }
    }
}
