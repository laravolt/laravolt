<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTableCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $signature = 'make:table {name} {--livewire}';

    protected $description = 'Create a new Table builder';

    protected function getStub()
    {
        if ($this->option('livewire')) {
            return platform_path('resources/stubs/table-livewire.stub');
        }

        return platform_path('resources/stubs/table.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('livewire')) {
            return $rootNamespace.'\Http\Livewire\Tables';
        }

        return $rootNamespace.'\Tables';
    }
}
