<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;

class AssetLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:link-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "public/laravolt" to "vendor/laravolt/ui/public"';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists(public_path('laravolt'))) {
            return $this->info('The "public/laravolt" directory already exists.');
        }

        $this->laravel->make('files')->link(
            __DIR__ . '/../public', public_path('laravolt')
        );

        $this->info('The [public/laravolt] directory has been linked.');
    }
}
