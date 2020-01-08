<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravolt\Suitable\Tables\BasicTable;
use Laravolt\Workflow\Tables\Table;
use Spatie\DataTransferObject\DataTransferObject;

class Module extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $processDefinitionKey;

    /** @var string */
    public $label;

    /** @var string|null */
    public $startTaskName;

    /** @var \Laravolt\Workflow\Tables\Table|\Laravolt\Suitable\Tables\BasicTable */
    public $table;

    /** @var array */
    public $whitelist;

    /** @deprecated */

    /** @var array */
    public $action;

    /** @var array */
    public $view;

    /** @var array */
    public $routes;

    public $index;

    /** @deprecated */
    public static function fromConfig(array $config): self
    {
        $data = collect($config)->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        })->toArray();
        $data['view'] = (array) ($data['view'] ?? null);
        $data['action'] = $data['action'] ?? [];
        $data['routes'] = $data['routes'] ?? [];

        $table = $data['table'] ?? null;
        if ($table) {
            $table = $table::make(null);
        } else {
            $table = BasicTable::make([]);
        }
        $data['table'] = $table;
        $module = new self($data);
        if ($table instanceof Table) {
            $module->table->setModule($module);
        }

        return $module;
    }

    public function getIndexUrl()
    {
        return route('camunda::process.index', $this->id);
    }

    public function getCreateUrl()
    {
        return route('camunda::process.create', $this->id);
    }

    public function getTasks()
    {
        $tasks = [];
        foreach ($this->whitelist as $whitelist) {
            if (is_array($whitelist)) {
                $whitelist = Arr::get($whitelist, 'task');
            }
            if (is_string($whitelist)) {
                $tasks[] = $whitelist;
            }
        }

        return $tasks;
    }

    public function getTask($task)
    {
        return Arr::get($this->normalizedTasks(), $task);
    }

    public function normalizedTasks()
    {
        $tasks = [];
        foreach ($this->whitelist as $whitelist) {
            if (is_array($whitelist)) {
                $tasks[$whitelist['task']] = $whitelist;
            }
            if (is_string($whitelist)) {
                $tasks[$whitelist] = ['task' => $whitelist];
            }
        }

        return $tasks;
    }

    public function getModel()
    {
        return \Laravolt\Workflow\Models\Module::where('key', $this->id)->rememberForever()->cacheDriver('array')->firstOrFail();
    }
}
