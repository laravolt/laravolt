<?php

namespace Laravolt\Workflow\Values;

use Illuminate\Support\Str;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Module extends DataTransferObject
{
    public string $id;

    public string $processDefinitionKey;

    public string $name;

    public string $table;

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
}
