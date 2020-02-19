<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTableCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $name = 'make:table {name}';

    protected $description = 'Create a new Table builder';

    protected function getStub()
    {
        return platform_path('resources/stubs/table.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Tables';
    }
}
