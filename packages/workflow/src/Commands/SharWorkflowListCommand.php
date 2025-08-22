<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowListCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:workflow:list 
                            {--status= : Filter by status (active, inactive)}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     */
    protected $description = 'List all SHAR workflows';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $status = $this->option('status');
        $format = $this->option('format');

        try {
            $workflows = $sharService->getWorkflows();

            // Filter by status if provided
            if ($status) {
                $workflows = $workflows->filter(function ($workflow) use ($status) {
                    return $workflow->status === $status;
                });
            }

            if ($workflows->isEmpty()) {
                $this->info('No workflows found.');
                return Command::SUCCESS;
            }

            if ($format === 'json') {
                $this->line(json_encode($workflows->toArray(), JSON_PRETTY_PRINT));
            } else {
                $this->info("Found {$workflows->count()} workflow(s):");
                
                $tableData = $workflows->map(function ($workflow) {
                    $stats = $workflow->getStatistics();
                    return [
                        $workflow->id,
                        $workflow->name,
                        "v{$workflow->version}",
                        ucfirst($workflow->status),
                        $stats['total_instances'],
                        $stats['running_instances'],
                        $workflow->created_at->format('Y-m-d H:i'),
                        $workflow->description ? \Str::limit($workflow->description, 30) : 'N/A',
                    ];
                })->toArray();

                $this->table(
                    ['ID', 'Name', 'Version', 'Status', 'Total Instances', 'Running', 'Created', 'Description'],
                    $tableData
                );
            }

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to list workflows: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}