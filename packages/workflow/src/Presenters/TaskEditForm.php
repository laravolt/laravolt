<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Presenters;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\Form;
use Laravolt\Workflow\Services\FormAdapter\FormAdapter;

class TaskEditForm
{
    protected $module;

    protected $task;

    protected $url;

    protected $processDefinition;

    protected $fields;

    /**
     * TaskForm constructor.
     */
    private function __construct(Module $module, $task)
    {
        $this->module = $module;
        $this->task = $task;

        $taskConfig = $this->module->getTask($task->task_name);
        if (!Arr::get($taskConfig, 'attributes.editable', true)) {
            throw new \DomainException('Task is non-editable');
        }

        $this->url = route('workflow::task.update', [$this->module->id, $this->task->task_id]);
        $this->processDefinition = ProcessDefinition::byKey($this->task->process_definition_key)->fetch();
        $this->fields = Form::getFields($this->task->process_definition_key, $this->task->task_name);
    }

    public static function make(Module $module, $task): self
    {
        return new static($module, $task);
    }

    public function title()
    {
        return $this->taskName();
    }

    public function key()
    {
        return $this->task->task_name;
    }

    public function getBindings()
    {
        $values = DB::table($this->task->form_type)->find($this->task->form_id);

        $bindings = $this->fields->pluck('field_name')->mapWithKeys(function ($item) use ($values) {
            return [$item => old($item, data_get($values, $item))];
        });

        return $bindings;
    }

    public function render()
    {
        try {
            $fields = Form::getFields($this->task->process_definition_key, $this->task->task_name);
            $values = DB::table($this->task->form_type)->find($this->task->form_id);
            $formDefinition = (new FormAdapter($fields, $values))->toArray();
            $output = form()->put($this->url)->id($this->task->task_name)
                .form()->hidden('_process_definition_key', $this->task->process_definition_key)
                .form()->hidden('_task_name', $this->task->task_name)
                .form()->hidden('_form_name', $this->task->form_type)
                .form()->make($formDefinition)
                .form()->action(
                    form()->submit('Simpan'), form()->link('Kembali',
                    route('workflow::process.show', [$this->module->id, $this->task->process_instance_id]))
                )
                .form()->close();

            return $output;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function processName(): string
    {
        return $this->processDefinition->name ?? $this->processDefinition->key;
    }

    public function taskName(): string
    {
        return Str::humanize($this->task->task_name);
    }

    public function getFields()
    {
        return $this->fields;
    }
}
