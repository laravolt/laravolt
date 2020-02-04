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

    /** @var array */
    public $action;

    /** @var array */
    public $view;

    /** @var array */
    public $routes;

    public $index;

    public static function make($id): self
    {
        $module = config("workflow-modules.$id");
        $table = $module['table'] ?? null;

        if (!$module) {
            if (config('app.debug')) {
                throw new \DomainException("File config config/workflow-modules/$id.php belum dibuat atau jalankan command `php artisan app:sync-module` terlebih dahulu untuk sinkronisasi Modul.");
            }
            abort(404);
        }

        $module['id'] = $module['id'] ?? $id;
        $module['action'] = $module['action'] ?? [];

        return static::fromConfig($module);
    }

    public static function fromConfig(array $config): self
    {
        $data = collect($config)->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        })->toArray();
        $data['view'] = (array) ($data['view'] ?? null);
        $data['action'] = $data['action'] ?? [];
        $data['routes'] = $data['routes'] ?? [];
        $data['whitelist'] = $data['whitelist'] ?? [];

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
        return route('workflow::process.index', $this->id);
    }

    public function getCreateUrl()
    {
        return route('workflow::process.create', $this->id);
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
        return \Laravolt\Workflow\Models\Module::where('key', $this->id)
            ->rememberForever()
            ->cacheDriver('array')
            ->firstOrFail();
    }
}
