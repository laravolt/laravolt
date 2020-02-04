<?php

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AuthUsername extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:auth-identifier {identifier=email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logged user in with username identifier';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $authConfig = base_path('/config/laravolt/auth.php');

        if (file_exists($authConfig) && Schema::hasColumn('users', 'username')) {
            $this->info('Authenticated user with username identifier');

            $arg = $this->argument('identifier') == 'both'
                    ? "['email', 'username']" : "'{$this->argument('identifier')}'";
            $replacement = "\n\t'identifier'   => {$arg},";

            file_put_contents($authConfig, preg_replace(
                "/\s*'identifier'\s*=>.*/m",
                $replacement,
                file_get_contents($authConfig)
            ));


        } else {
            $this->error('Make sure you have published Laravolt Platform package');
        }
    }
}
