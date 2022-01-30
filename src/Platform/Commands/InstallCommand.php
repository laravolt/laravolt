<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare Laravolt skeleton files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->addEntries();
        Artisan::call(LinkCommand::class);
        Artisan::call('vendor:publish', ['--tag' => 'laravolt-skeleton', '--force' => true]);
        Artisan::call('vendor:publish', ['--tag' => 'laravolt-migrations']);
        Artisan::call('vendor:publish', ['--tag' => 'laravolt-assets']);
        Artisan::call('vendor:publish', ['--tag' => 'setting']);
        Artisan::call(
            'vendor:publish',
            ['--tag' => 'migrations', '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider']
        );

        $this->newLine();
        $this->info(sprintf('Application ready: %s', url('/')));

        return 1;
    }

    private function addEntries()
    {
        $entries = [
            base_path('.gitignore') => [
                '/public/laravolt',
            ],
        ];

        foreach ($entries as $file => $lines) {
            $contents = explode("\n", file_get_contents($file));
            foreach ($lines as $line) {
                // Only put entry if not exists
                if (! in_array($line, $contents, true)) {
                    file_put_contents($file, $line."\n", FILE_APPEND);
                }
            }
        }
    }
}
