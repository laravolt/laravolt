<?php

declare(strict_types=1);

namespace Laravolt\Media\Console;

use Illuminate\Console\Command;
use Laravolt\Media\Jobs\CleanupStaleChunksJob;

class CleanupStaleChunksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'media:cleanup-chunks
                            {--hours=24 : Hours after which chunks are considered stale}
                            {--queue : Dispatch the cleanup job to queue instead of running synchronously}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up stale chunked upload files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $useQueue = $this->option('queue');

        if ($hours <= 0) {
            $this->error('Hours must be a positive integer.');
            return 1;
        }

        $this->info("Cleaning up chunks older than {$hours} hours...");

        if ($useQueue) {
            CleanupStaleChunksJob::dispatch($hours);
            $this->info('Cleanup job has been dispatched to the queue.');
        } else {
            $job = new CleanupStaleChunksJob($hours);
            $job->handle();
            $this->info('Cleanup completed successfully.');
        }

        return 0;
    }
}
