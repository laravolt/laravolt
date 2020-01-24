<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Presenters;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravolt\Camunda\Models\Task;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Enum\TaskStatus;
use Laravolt\Workflow\Models\AutoSave;
use Laravolt\Workflow\Models\Form;
use Laravolt\Workflow\Services\FormAdapter\FormAdapter;
use Laravolt\Workflow\Traits\DataRetrieval;

class TaskForm
{
    use DataRetrieval;

    const CREATE = 'StartForm.CREATE';

    const EDIT = 'StartForm.EDIT';

    protected $module;

    protected $task;

    protected $taskConfig;

    protected $processDefinition;

    protected $processInstance;

    protected $url;

    protected $fields;

    protected $data;

    protected $mode = self::CREATE;

    /**
     * TaskForm constructor.
     */
    private function __construct(Module $module, Task $task)
    {
        $this->task = $task;
        $this->taskConfig = $module->getTask($this->task->taskDefinitionKey);
        $this->url = route('workflow::task.store', [$module->id, $this->task->id]);
        $this->processInstance = $this->task->processInstance();
        $this->processDefinition = $this->processInstance->processDefinition();
        $this->fields = Form::getFields($this->processDefinition->key, $this->task->taskDefinitionKey);

        $autoSave = AutoSave::query()
            ->where('task_id', $this->task->id)
            ->first();
        $autoSaveData = optional($autoSave)->data ?? [];
        $processData = $this->getDataByProcessInstanceId($this->processInstance->id);

        $this->data = $autoSaveData + $processData;
    }

    public static function make(Module $module, Task $task): self
    {
        return new static($module, $task);
    }

    public function title()
    {
        return $this->taskConfig['label'] ?? $this->task->name;
    }

    public function key()
    {
        return $this->task->taskDefinitionKey;
    }

    public function render()
    {
        try {
            $taskName = $formName = $this->task->taskDefinitionKey;

            $definition = (new FormAdapter($this->fields, $this->data))->toArray();
            $draft = Arr::get($this->data, 'status') == TaskStatus::DRAFT;

            if (!$draft) {
                $definition = collect($definition)->transform(function ($item) {

                    // Uncomment this if:
                    // Value yang sudah diisikan di task sebelumnya tidak bisa diedit
                    // if (Arr::get($item, 'value') !== null && Arr::get($item, 'type') !== 'checkbox') {
                    //     $item['readonly'] = true;
                    // }

                    return $item;
                })->toArray();
            }

            $output = form()
                    ->open($this->url)
                    ->setHiddenMethod($this->method())
                    ->id($this->task->taskDefinitionKey)
                    ->data('form-task', $taskName)
                    ->addClass(config('laravolt.workflow.form.class'))
                .form()->hidden('_process_definition_key', $this->processDefinition->key)
                .form()->hidden('_process_instance_id', $this->processInstance->id)
                .form()->hidden('_task_name', $taskName)
                .form()->hidden('_form_name', $formName)
                .form()->html($this->getStatus())
                .form()->make($definition)
                .form()->action(
                    form()->submit('Simpan'),
                    form()->button('Simpan Sebagai Draf', '_draft')->value(1)->attribute('type', 'submit')
                )
                .form()->close();

            return $output;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getBindings()
    {
        $values = $this->data;

        return $this->fields->pluck('field_name')->mapWithKeys(function ($item) use ($values) {
            return [trim($item) => old($item, Arr::get($values, $item))];
        });
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getProcessVariables($includeCurrentTask = false)
    {
        $query = DB::table('camunda_task')
            ->where('process_instance_id', $this->processInstance->id);

        if (!$includeCurrentTask) {
            $query->where('task_name', '<>', $this->task->taskDefinitionKey);
        }

        $mapping = $query->latest()->get(['form_type', 'form_id']);

        $variables = [];

        foreach ($mapping as $map) {
            $data = collect((array) DB::table($map->form_type)->find($map->form_id))
                ->except(['id', 'created_at', 'created_by', 'updated_at', 'updated_by'])
                ->toArray();
            $variables += $data;
        }

        return $variables;
    }

    protected function getStatus()
    {
        $status = DB::table('camunda_task')->where('task_id', $this->task->id)->value('status') ?? TaskStatus::NEW;
        $status = new TaskStatus($status);
        $color = '';

        switch ($status) {
            case TaskStatus::NEW:
                $color = 'basic';

                break;
            case TaskStatus::DRAFT:
                $color = 'basic yellow';

                break;
        }

        return "<div class='field'><div class='ui label {$color}'>{$status->description}</div></div>";
    }

    protected function getValues($formName)
    {
        if (!$this->task) {
            return [];
        }

        return (array) DB::table($formName)
            ->join('camunda_task', 'camunda_task.task_id', '=', "$formName.task_id")
            ->where('camunda_task.task_id', $this->task->id)
            ->first();
    }

    protected function method()
    {
        return ($this->mode === static::CREATE) ? 'POST' : 'PUT';
    }
}
