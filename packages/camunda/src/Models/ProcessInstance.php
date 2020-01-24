<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProcessInstance extends CamundaModel
{
    protected $processDefinition;

    protected $parentProcessInstance;

    public function processDefinition(): ?ProcessDefinition
    {
        $id = $key = null;
        if (!$this->processDefinition) {
            $id = $this->processDefinitionId ?? $this->definitionId ?? null;

            if (!$id) {
                $key = DB::table('camunda_task')
                    ->where('process_instance_id', $this->id)
                    ->value('process_definition_key');
            }

            if ($id) {
                $this->processDefinition = (new ProcessDefinition($id))->fetch();
            } elseif ($key) {
                $this->processDefinition = ProcessDefinition::byKey($key)->fetch();
            }
        }

        return $this->processDefinition;
    }

    public function parent()
    {
        if ($this->parentProcessInstance === null) {
            $url = 'history/process-instance?subProcessInstanceId='.$this->id;
            $result = Arr::first($this->get($url));

            if ($result !== null) {
                $this->parentProcessInstance = new ProcessInstanceHistory($result->id, $result);
            }
        }

        return $this->parentProcessInstance;
    }

    public function currentTask()
    {
        $tasks = $this->tasks();

        return count($tasks) > 0 ? new Task($tasks[0]->id, $tasks[0]) : null;
    }

    public function tasks(array $whitelist = [])
    {
        $url = 'task/?processInstanceId='.$this->id;

        if (!empty($whitelist)) {
            $whitelist = implode(',', $whitelist);
            $url .= '&taskDefinitionKeyIn='.$whitelist;
        }

        $tasks = $this->get($url);

        $data = [];
        foreach ($tasks as $task) {
            $task = new Task($task->id, $task);
            $task->setProcessInstance($this);
            $data[] = $task;
        }

        return $data;
    }

    public function setVariable($key, $value, $type = 'String')
    {
        $this->put('variables/'.$key, [
            'type' => $type,
            'value' => $value,
        ], true);
    }

    public function setVariables(array $modifications, array $deletions = [])
    {
        $modifications = $this->formatVariables($modifications);
        $deletions = $this->formatVariables($deletions);

        $this->post('variables', [
            'modifications' => $modifications,
            'deletions' => $deletions,
        ], true);
    }

    public function getInfo()
    {
        return $this->get('');
    }

    public function getVariable($key)
    {
        return $this->get('variables/'.$key);
    }

    public function getVariables()
    {
        return get_object_vars($this->get('variables'));
    }

    public function deleteProcessInstance()
    {
        return $this->delete('');
    }

    public function ended()
    {
        return $this->get('history/process-instance/?processInstanceId='.$this->id)[0]->state == 'COMPLETED';
    }

    public function getEndEventId()
    {
        return optional(Arr::first($this->get('history/activity-instance/?processInstanceId='.$this->id.'&activityType=noneEndEvent')))->activityId;
    }

    public function modify($data)
    {
        return $this->post('modification', $data, true);
    }

    public function getSubProcess()
    {
        $subProcess = $this->get('process-instance?superProcessInstance='.$this->id);
        $data = [];
        foreach ($subProcess as $sub) {
            $data[] = new self($sub->id, $sub);
        }

        return $data;
    }

    public function suspend()
    {
        $data = [
            'suspended' => true,
        ];

        return $this->put('suspended', $data, true);
    }
}
