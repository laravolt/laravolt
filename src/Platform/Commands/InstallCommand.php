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
        $this->info('Starting Laravolt installation...');

        $this->addEntries();
        Artisan::call(ExtractAssetsCommand::class);
        Artisan::call(LinkCommand::class);

        $this->info('1/2: Publishing Laravolt skeleton files...');

        Artisan::call('vendor:publish', ['--tag' => 'laravolt-skeleton', '--force' => true]);
        Artisan::call('vendor:publish', ['--tag' => 'laravolt-migrations']);
        Artisan::call('vendor:publish', ['--tag' => 'laravolt-assets']);
        Artisan::call(
            'vendor:publish',
            ['--tag' => 'migrations', '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider']
        );

        $this->info('2/2: PestPHP installation...');

        // Install Pest v4 for modern testing
        Artisan::call(Pest4InstallCommand::class);

        $this->info(sprintf('Application ready: %s', url('/')));

        $this->info('Done. Laravolt installation complete, please run this command to finish the installation:');

        $this->line('php artisan migrate:fresh');

        return self::SUCCESS;
    }

    private function addEntries()
    {
        $entries = [
            base_path('.gitignore') => [
                '/public/laravolt',
                '/build/coverage',
                '/pestphp-coverage-result.xml',
                '/pestphp-execution-result.xml',
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
