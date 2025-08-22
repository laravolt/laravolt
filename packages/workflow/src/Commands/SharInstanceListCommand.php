<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharInstanceListCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:instance:list 
                            {--workflow= : Filter by workflow name}
                            {--status= : Filter by status (running, completed, failed)}
                            {--limit=20 : Limit number of results}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     */
    protected $description = 'List SHAR workflow instances';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $workflowName = $this->option('workflow');
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $format = $this->option('format');

        try {
            $instances = $sharService->getWorkflowInstances($workflowName);

            // Filter by status if provided
            if ($status) {
                $instances = $instances->filter(function ($instance) use ($status) {
                    return $instance->status === $status;
                });
            }

            // Apply limit
            if ($limit > 0) {
                $instances = $instances->take($limit);
            }

            if ($instances->isEmpty()) {
                $this->info('No workflow instances found.');
                return Command::SUCCESS;
            }

            if ($format === 'json') {
                $this->line(json_encode($instances->toArray(), JSON_PRETTY_PRINT));
            } else {
                $this->info("Found {$instances->count()} instance(s):");
                
                $tableData = $instances->map(function ($instance) {
                    $duration = $instance->getDurationInSeconds();
                    $durationStr = $duration ? "{$duration}s" : 'N/A';
                    
                    return [
                        \Str::limit($instance->id, 12),
                        $instance->workflow_name,
                        ucfirst($instance->status),
                        $instance->started_at ? $instance->started_at->format('Y-m-d H:i') : 'N/A',
                        $instance->completed_at ? $instance->completed_at->format('Y-m-d H:i') : 'N/A',
                        $durationStr,
                        count($instance->variables ?? []),
                    ];
                })->toArray();

                $this->table(
                    ['Instance ID', 'Workflow', 'Status', 'Started', 'Completed', 'Duration', 'Variables'],
                    $tableData
                );
            }

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to list instances: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}