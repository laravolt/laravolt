<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Camunda\Dto\ProcessInstance as ProcessInstanceDto;
use Laravolt\Camunda\Http\ProcessInstanceClient;
use Laravolt\Workflow\Models\Collections\VariableCollection;

class ProcessInstance extends Model
{
    protected $table = 'wf_process_instances';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tasks' => 'array',
        'variables' => VariableCollection::class,
    ];

    public function definition()
    {
        return $this->belongsTo(ProcessDefinition::class, 'definition_id');
    }

    public static function sync(ProcessInstanceDto $instance, array $variables = []): self
    {
        $tasks = collect(ProcessInstanceClient::tasks($instance->id))->pluck('taskDefinitionKey');

        return ProcessInstance::updateOrCreate(
            [
                'id' => $instance->id,
            ],
            [
                'definition_id' => $instance->definitionId,
                'definition_key' => \Str::of($instance->definitionId)->before(':'),
                'business_key' => $instance->businessKey,
                'tasks' => $tasks,
                'variables' => $instance->variables ?? $variables,
            ]
        );
    }
}
