<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeViewCommnad extends GeneratorCommand
{
    protected $type = 'View';

    protected $signature = 'make:view {name} {--force} {--title=}';

    protected $description = 'Create a new blade view';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $path = $this->viewPath(Str::of($this->getNameInput())->replace('.', '/')).'.blade.php';

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force')
                || ! $this->option('force'))
            && $this->files->exists($path)
        ) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildViewStub());

        $this->info($this->type.' created successfully.');
    }

    protected function getStub()
    {
        return platform_path('resources/stubs/view.stub');
    }

    private function buildViewStub(): string
    {
        $content = $this->files->get($this->getStub());

        return (string) Str::of($content)->replace('{title}', $this->option('title') ?? 'Please Edit This');
    }
}
