<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Presenters;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\Form;
use Laravolt\Workflow\Services\FormAdapter\FormAdapter;

class TaskInfo
{
    protected $module;

    protected $task;

    protected $taskConfig;

    protected $attributes = [
        'active' => false,
    ];

    private function __construct(Module $module, $task)
    {
        $this->module = $module;
        $this->task = $task;
        $this->taskConfig = $module->getTask($task->task_name);
        $this->attributes = Arr::get($module->getTask($task->task_name), 'attributes', []) + $this->attributes;
    }

    public static function make(Module $module, $task)
    {
        return new static($module, $task);
    }

    public function render()
    {
        $fields = Form::getFields($this->task->process_definition_key, $this->task->task_name);
        $formName = $this->task->form_type;
        $values = (array) DB::table($formName)->find($this->task->form_id);
        if (empty($values)) {
            return null;
        }

        // Filter field yang ditampilkan berdasar config "only"
        $onlyColumns = $this->taskConfig['only'] ?? null;
        $exceptColumns = $this->taskConfig['except'] ?? null;

        if ($onlyColumns) {
            $fields = $fields->filter(function ($item) use ($onlyColumns) {
                return in_array($item->field_name, $onlyColumns);
            });
        }

        if ($exceptColumns) {
            $fields = $fields->reject(function ($item) use ($exceptColumns) {
                return in_array($item->field_name, $exceptColumns);
            });
        }

        $formDefinition = (new FormAdapter($fields, $values))->readonly()->toArray();
        $task = $this->module->getTask($this->task->task_name);
        // dump($formDefinition);

        //coba damar
        foreach ($formDefinition as $keys => $lists) {
            foreach ($lists as $key => $list) {
                if ($key == 'data') {
                    foreach ($list as $k => $v) {
                        foreach ($v as $p => $vl) {
                            $str = preg_replace('/["\[\]]/', '', $vl);
                            $formDefinition[$keys][$key][$k][$p] = $str;
                        }
                    }
                }
            }
        }

        return view('workflow::components.task-info', [
            'task' => $this->task,
            'taskConfig' => $task,
            'module' => $this->module,
            'title' => $task['label'] ?? Str::humanize($this->task->task_name),
            'formDefinition' => $formDefinition,
            'values' => $values,
            'editable' => Arr::get($task, 'attributes.editable', true) && $this->task->task_id,
            'attributes' => $this->attributes,
        ]);
    }
}
