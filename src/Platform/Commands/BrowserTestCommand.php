<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BrowserTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:test:browser
        {path=tests/Browser : Browser test path to run}
        {--debug : Run browser tests in headed mode and enable browser assertion debugging}
        {--browser=chrome : Browser name to use: chrome, firefox, or safari}
        {--parallel : Run browser tests in parallel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Laravolt browser tests with Pest and Playwright';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $browser = (string) $this->option('browser');

        if (! in_array($browser, ['chrome', 'firefox', 'safari'], true)) {
            $this->error("Unknown browser [{$browser}]. Use chrome, firefox or safari.");

            return self::FAILURE;
        }

        $command = [base_path('vendor/bin/pest'), (string) $this->argument('path'), '--no-coverage'];

        if ($this->option('debug')) {
            $command[] = '--debug';
            $command[] = '--headed';
        }

        $command[] = '--browser';
        $command[] = $browser;

        if ($this->option('parallel')) {
            $command[] = '--parallel';
        }

        $result = Process::path(base_path())
            ->forever()
            ->run($command, fn (string $type, string $output) => $this->output->write($output));

        return $result->successful() ? self::SUCCESS : self::FAILURE;
    }
}
