<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableSeparator;

class MakeTableCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $signature = 'make:table {name} {--legacy}';

    protected $description = 'Create a new Table builder';

    public function handle()
    {
        $defaultHandler = parent::handle();

        $slug = Str::kebab($this->getNameInput());
        $info = [
            ['Class', '<info>'.$this->qualifyClass($this->getNameInput()).'</info>'],
            new TableSeparator(),
            ['Blade Component', "<info><livewire:table.$slug /></info>"],
            new TableSeparator(),
            ['Blade Directive', "<info>@livewire('table.$slug')</info>"],
        ];
        $this->table([], $info);

        return $defaultHandler;
    }

    protected function getStub()
    {
        if ($this->option('legacy')) {
            return platform_path('resources/stubs/table-legacy.stub');
        }

        return platform_path('resources/stubs/table.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('legacy')) {
            return $rootNamespace.'\Table';
        }

        return $rootNamespace.'\Http\Livewire\Table';
    }
}
