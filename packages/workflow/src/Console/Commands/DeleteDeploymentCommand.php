<?php

namespace Laravolt\Workflow\Console\Commands;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use Laravolt\Camunda\Models\Deployment;
use Laravolt\Workflow\Models\Bpmn;

class DeleteDeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:delete-deployment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cascade deployment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $deployments = Deployment::all();
        $deleted = 0;
        foreach ($deployments as $deployment) {
            try {
                $deployment->delete("deployment/{$deployment->id}?cascade=true");
                Bpmn::query()->where('deployment_id', $deployment->id)->delete();
                $deleted++;
            } catch (ClientException | ServerException $e) {
                $this->error(json_decode((string) $e->getResponse()->getBody())->message ?? $e->getMessage());
            }
        }

        $this->info(sprintf('%d deployment(s) deleted', $deleted));
    }
}
