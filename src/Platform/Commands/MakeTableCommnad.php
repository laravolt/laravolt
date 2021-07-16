<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableSeparator;
use function Laravolt\platform_path;

class MakeTableCommnad extends GeneratorCommand
{
    protected $type = 'Class';

    protected $signature = 'make:table {name} {--legacy}';

    protected $description = 'Create a new Table builder';

    public function handle()
    {
        $defaultHandler = parent::handle();

        $namespace = (string) Str::of($this->getDefaultNamespace($this->rootNamespace()))
            ->replace('\\\\', '\\')
            ->append('\\');

        $qualifyClass = $this->qualifyClass($this->getNameInput());
        $slug = Str::of($qualifyClass)
            ->after($namespace)
            ->explode('\\')
            ->map(fn ($class) => Str::kebab($class))
            ->implode('.');

        $classPathConstant = '\\'.$qualifyClass.'::class';

        $info = [
            ['Blade component', "<livewire:table.$slug />"],
            new TableSeparator(),
            ['Blade directive using alias', "@livewire('table.$slug')"],
            new TableSeparator(),
            ['Blade directive using class path <warning>(recommended)</warning>', "@livewire($classPathConstant)"],
        ];

        $this->warn($qualifyClass);
        $this->table(['Usage As', 'Code Snippet'], $info);

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
