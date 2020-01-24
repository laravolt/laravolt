<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Presenters;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Camunda\Models\ProcessInstance;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\Form;
use Laravolt\Workflow\Services\FormAdapter\FormAdapter;

class StartForm
{
    const CREATE = 'StartForm.CREATE';

    const EDIT = 'StartForm.EDIT';

    protected $module;

    protected $taskConfig;

    protected $processDefinition;

    protected $processInstance;

    protected $url;

    protected $mode = self::CREATE;

    protected $fields;

    protected $formName;

    /**
     * StartForm constructor.
     */
    private function __construct(
        Module $module,
        ProcessDefinition $processDefinition,
        ?ProcessInstance $processInstance
    ) {
        $this->processDefinition = $processDefinition;
        $this->module = $module;
        $this->url = route('workflow::process.store', $this->module->id);

        $this->processInstance = $processInstance;
        if ($this->processInstance) {
            $this->mode = self::EDIT;
            $this->url = route('workflow::process.update', [$this->module->id, $this->processInstance->id]);
        }

        $this->fields = Form::getFields($this->processDefinition->key, $this->module->startTaskName);
        $this->formName = Form::getFormName($this->processDefinition->key, $this->module->startTaskName);

        $this->taskConfig = $module->getTask($this->module->startTaskName);
    }

    public static function make(
        Module $module,
        ProcessDefinition $processDefinition,
        ?ProcessInstance $processInstance = null
    ): self {
        $form = new static($module, $processDefinition, $processInstance);

        return $form;
    }

    public static function makeFromInstance(Module $module, ProcessInstance $processInstance)
    {
        $mapping = DB::table('camunda_task')
            ->where('process_instance_id', $processInstance->id)
            ->whereNull('task_id')
            ->first();

        if (empty($mapping)) {
            throw (new ModelNotFoundException())->setModel($processInstance);
        }

        $form = static::make($module, $processInstance->processDefinition(), $processInstance);

        return $form;
    }

    public function processName(): string
    {
        return $this->processDefinition->name ?? $this->processDefinition->key;
    }

    public function title(): string
    {
        return $this->taskConfig['label'] ?? $this->taskName();
    }

    public function taskName(): string
    {
        return Str::humanize($this->module->startTaskName);
    }

    public function render(): string
    {
        $formName = $this->formName;
        $values = $this->getValues($formName);
        $formDefinition = (new FormAdapter($this->fields, $values))->toArray();
        $output = form()
                ->open($this->url)
                ->setHiddenMethod($this->method())
                ->addClass(config('laravolt.workflow.form.class'))
            .form()->hidden('_process_definition_key', $this->module->processDefinitionKey)
            .form()->hidden('_task_name', $this->module->startTaskName)
            .form()->make($formDefinition)
            .form()->action(
                [
                    form()->submit('<i class="icon send"></i>  Simpan'),
                    $this->backButton('Kembali'),
                ]
            )
            .form()->close();

        return $output;
    }

    public function getBindings()
    {
        $values = $this->getValues($this->formName);

        return $this->fields->pluck('field_name')->mapWithKeys(function ($item) use ($values) {
            return [$item => old($item, Arr::get($values, $item))];
        });
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getProcessDefinition()
    {
        return $this->processDefinition;
    }

    public function getTaskName()
    {
        return $this->module->startTaskName;
    }

    protected function method()
    {
        return ($this->mode === static::CREATE) ? 'POST' : 'PUT';
    }

    protected function backButton(string $label)
    {
        $backButton = '';
        if ($this->processInstance) {
            $backButton = form()->link($label,
                route('workflow::process.show', [$this->module->id, $this->processInstance->id]));
        }

        return $backButton;
    }

    protected function getValues($formName)
    {
        if (!$this->processInstance) {
            return [];
        }

        return (array) DB::table($formName)
            ->whereNull('task_id')
            ->where('process_instance_id', $this->processInstance->id)
            ->first();
    }
}
