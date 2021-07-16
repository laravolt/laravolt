<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableSeparator;
use function Laravolt\platform_path;

class MakeChartCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $name = 'make:chart {name}';

    protected $description = 'Create a new chart component';

    public function handle()
    {
        $defaultHandler = parent::handle();

        $slug = Str::kebab($this->getNameInput());
        $info = [
            ['Class', '<info>'.$this->qualifyClass($this->getNameInput()).'</info>'],
            new TableSeparator(),
            ['Blade Component', "<info><livewire:chart.$slug /></info>"],
            new TableSeparator(),
            ['Blade Directive', "<info>@livewire('chart.$slug')</info>"],
        ];
        $this->table([], $info);

        return $defaultHandler;
    }

    protected function getStub()
    {
        return platform_path('resources/stubs/chart.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Livewire\Chart';
    }
}
