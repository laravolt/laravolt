<?php

namespace Laravolt\Workflow\Entities;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravolt\Camunda\Dto\Task;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Module extends DataTransferObject
{
    public string $id;

    public string $processDefinitionKey;

    public string $name;

    public string $table;

    public array $tasks;

    /**
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public static function make(string $id): self
    {
        $config = config("laravolt.workflow-modules.$id");

        if (!$config) {
            throw new \DomainException(
                "File config config/laravolt/workflow-modules/$id.php belum dibuat atau jalankan command `php artisan app:sync-module` terlebih dahulu untuk sinkronisasi Modul."
            );
        }

        // convert snake_case key to camelCase
        $config = collect($config)
            ->mapWithKeys(fn ($item, $key) => [Str::camel($key) => $item])
            ->toArray();

        return new self(['id' => $id] + $config);
    }

    /**
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     *
     * @return self[]
     */
    public static function discover(): array
    {
        $modules = [];
        foreach (config('laravolt.workflow-modules') as $id => $config) {
            $modules[] = self::make($id);
        }

        return $modules;
    }

    public function startFormSchema(): array
    {
        $startForm = array_key_first($this->tasks);

        return config("laravolt.workflow-forms.{$this->id}.$startForm");
    }

    public function startForm(): string
    {
        return form()->make($this->startFormSchema())->render();
    }

    public function formSchema(string $taskDefinitionKey): array
    {
        return config("laravolt.workflow-forms.{$this->id}.$taskDefinitionKey", []);
    }

    public function registerTaskEvents(Task $task): void
    {
        $listeners = config("laravolt.workflow-modules.{$this->id}.tasks.$task->taskDefinitionKey.listeners", []);
        foreach ($listeners as $event => $handlers){
            foreach ($handlers as $handler) {
                Event::listen($event, $handler);
            }
        }
    }
}
