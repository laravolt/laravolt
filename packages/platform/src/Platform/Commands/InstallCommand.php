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

        $username = 'admin@example.com';
        $password = 'asdf1234';

        Artisan::call(
            AdminCommand::class,
            [
                'name' => 'Administrator',
                'email' => $username,
                'password' => $password,

            ]);

        $this->newLine();
        $this->info('Application ready...');
        $this->info(sprintf('URL        : %s', url('/')));
        $this->info(sprintf('Login      : %s', $username));
        $this->info(sprintf('Password   : %s', $password));

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
            foreach ($lines as $line) {
                file_put_contents($file, $line."\n", FILE_APPEND);
            }
        }
    }
}
