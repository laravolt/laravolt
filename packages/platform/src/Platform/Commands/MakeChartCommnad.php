<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeChartCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $name = 'make:chart {name}';

    protected $description = 'Create a new chart component';

    protected function getStub()
    {
        return platform_path('resources/stubs/chart.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Livewire\Chart';
    }
}
