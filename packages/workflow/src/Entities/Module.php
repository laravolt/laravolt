<?php

namespace Laravolt\Workflow\Entities;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravolt\Workflow\Livewire\ProcessInstancesTable;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Module extends DataTransferObject
{
    public string $id;

    public string $processDefinitionKey;

    public string $name;

    public string $table;

    public array $tableVariables;

    public array $trackerVariables;

    public array $trackerTasks;

    public array $tasks;

    /**
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public static function make(string $id): self
    {
        $config = config("laravolt.workflow-modules.$id");

        if (! $config) {
            throw new \DomainException(
                "File config config/laravolt/workflow-modules/$id.php belum dibuat."
            );
        }

        $config['table'] ??= ProcessInstancesTable::class;
        $config['table_variables'] ??= [];

        // convert snake_case key to camelCase
        $config = collect($config)
            ->mapWithKeys(fn ($item, $key) => [Str::camel($key) => $item])
            ->toArray();

        return new self(['id' => $id] + $config);
    }

    /**
     * @return self[]
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public static function discover(): array
    {
        $modules = [];
        foreach (config('laravolt.workflow-modules') as $id => $config) {
            $modules[] = self::make($id);
        }

        return $modules;
    }

    public function startTaskKey(): string
    {
        return array_key_first($this->tasks);
    }

    public function startFormSchema(): array
    {
        return $this->formSchema($this->startTaskKey());
    }

    public function startForm(string $url = null, array $data = []): string
    {
        $url ??= route('workflow::module.instances.store', $this->id);

        $html = form()->post($url)->multipart()->horizontal()->data('role', 'start-form');
        $html .= form()->make($this->startFormSchema())->bindValues($data)->render();
        $html .= form()->action(form()->submit(request('_action', 'Simpan'))->addClass(config('laravolt.ui.color')));
        $html .= form()->close();

        return $html;
    }

    public function formSchema(string $taskDefinitionKey): array
    {
        return config("laravolt.workflow-modules.{$this->id}.tasks.$taskDefinitionKey.form_schema", []);
    }

    public function trackerFormSchema(): array
    {
        return collect(config("laravolt.workflow-modules.{$this->id}.tasks", []))
            ->pluck('form_schema')
            ->flatMap(function ($item) {
                return $item;
            })
            ->only($this->trackerVariables)
            ->toArray();
    }

    public function message(string $taskDefinitionKey, string $type, string $default = ''): string
    {
        return config("laravolt.workflow-modules.{$this->id}.tasks.$taskDefinitionKey.messages.$type", $default);
    }

    public function registerTaskEvents(string $taskKey): void
    {
        $listeners = config("laravolt.workflow-modules.{$this->id}.tasks.$taskKey.listeners", []);
        foreach ($listeners as $event => $handlers) {
            foreach ($handlers as $handler) {
                Event::listen($event, $handler);
            }
        }
    }
}
