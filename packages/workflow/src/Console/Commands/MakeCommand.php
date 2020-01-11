<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'workflow:make';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create new workflow module';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('Human friendly name');
        $key = $this->ask('Process definition key');
        //@TODO: generate config file template
    }
}
