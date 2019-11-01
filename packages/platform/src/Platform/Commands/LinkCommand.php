<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;

class LinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:link';

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
        if (!file_exists(public_path('laravolt'))) {
            $this->laravel->make('files')->link(
                platform_path('public'), public_path('laravolt')
            );
        }

        $this->info('The [public/laravolt] directory has been linked.');
    }
}
