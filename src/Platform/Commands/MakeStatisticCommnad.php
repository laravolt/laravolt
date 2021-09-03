<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableSeparator;
use function Laravolt\platform_path;

class MakeStatisticCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $name = 'make:statistic {name}';

    protected $description = 'Create a new statistic component';

    public function handle()
    {
        $defaultHandler = parent::handle();

        $slug = Str::kebab($this->getNameInput());
        $info = [
            ['Class', '<info>'.$this->qualifyClass($this->getNameInput()).'</info>'],
            new TableSeparator(),
            ['Blade Component', "<info><livewire:statistic.$slug /></info>"],
            new TableSeparator(),
            ['Blade Directive', "<info>@livewire('statistic.$slug')</info>"],
        ];
        $this->table([], $info);

        return $defaultHandler;
    }

    protected function getStub()
    {
        return platform_path('resources/stubs/statistic.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Livewire\Statistic';
    }
}
