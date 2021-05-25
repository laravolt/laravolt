<?php

namespace Laravolt\Workflow\Entities;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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

    public function startTaskKey(): string
    {
        return array_key_first($this->tasks);
    }

    public function startFormSchema(): array
    {
        return config("laravolt.workflow-forms.{$this->id}.{$this->startTaskKey()}");
    }

    public function startForm(): string
    {
        return form()->make($this->startFormSchema())->render();
    }

    public function formSchema(string $taskDefinitionKey): array
    {
        return config("laravolt.workflow-forms.{$this->id}.$taskDefinitionKey", []);
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
