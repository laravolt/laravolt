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
        /** @var \Illuminate\Filesystem\Filesystem $files */
        $files = $this->laravel->make('files');
        $target = public_path('laravolt');

        // Check if symlink exists and remove it first
        if (file_exists($target) || is_link($target)) {
            if (is_link($target)) {
                $files->delete($target);
                $this->info('Removed existing symlink at [public/laravolt].');
            } else {
                // If it's a directory and not a symlink, we should handle it appropriately
                $this->error('A non-symlink file/directory exists at [public/laravolt]. Please remove it manually.');

                return;
            }
        }

        // Create the new symlink
        $files->link(
            \Laravolt\platform_path('public'),
            $target
        );

        $this->info('The [public/laravolt] directory has been linked.');
    }
}
