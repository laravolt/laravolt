<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Camunda\Dto\ProcessInstance as ProcessInstanceDto;
use Laravolt\Camunda\Http\ProcessInstanceHistoryClient;
use Laravolt\Camunda\Http\TaskClient;
use Laravolt\Workflow\Models\Collections\VariableCollection;
use Str;

class ProcessInstance extends Model
{
    public $incrementing = false;

    protected $table = 'wf_process_instances';

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tasks' => 'array',
        'variables' => VariableCollection::class,
    ];

    public static function sync(ProcessInstanceDto $instance, array $variables = []): self
    {
        $tasks = collect(TaskClient::getByProcessInstanceId($instance->id))->pluck('taskDefinitionKey', 'id');
        $processInstanceHistory = ProcessInstanceHistoryClient::find($instance->id);

        return self::updateOrCreate(
            [
                'id' => $instance->id,
            ],
            [
                'definition_id' => $instance->definitionId,
                'definition_key' => Str::of($instance->definitionId)->before(':'),
                'business_key' => $instance->businessKey,
                'tasks' => $tasks,
                'variables' => $instance->variables ?? $variables,
                'created_at' => $processInstanceHistory->startTime,
            ]
        );
    }

    public function definition()
    {
        return $this->belongsTo(ProcessDefinition::class, 'definition_id');
    }

    public function getTrackingCode()
    {
        return $this->id;
    }
}
