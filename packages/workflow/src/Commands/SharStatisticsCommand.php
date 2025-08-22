<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflow;
use Laravolt\Workflow\SharWorkflowService;

class SharStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:statistics 
                            {--workflow= : Show statistics for specific workflow}
                            {--detailed : Show detailed breakdown}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     */
    protected $description = 'Show SHAR workflow statistics and analytics';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $workflowName = $this->option('workflow');
        $detailed = $this->option('detailed');
        $format = $this->option('format');

        try {
            if ($workflowName) {
                return $this->showWorkflowStatistics($sharService, $workflowName, $format);
            } else {
                return $this->showGlobalStatistics($sharService, $detailed, $format);
            }
        } catch (SharException $e) {
            $this->error("Failed to get statistics: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Show statistics for a specific workflow
     */
    private function showWorkflowStatistics(SharWorkflowService $sharService, string $workflowName, string $format): int
    {
        $workflow = $sharService->getWorkflow($workflowName);
        
        if (!$workflow) {
            $this->error("Workflow not found: {$workflowName}");
            return Command::FAILURE;
        }

        $statistics = $sharService->getWorkflowStatistics($workflowName);

        if ($format === 'json') {
            $data = [
                'workflow' => [
                    'name' => $workflow->name,
                    'version' => $workflow->version,
                    'status' => $workflow->status,
                    'created_at' => $workflow->created_at->toISOString(),
                ],
                'statistics' => $statistics,
            ];
            
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
        } else {
            $this->info("Statistics for workflow: {$workflowName}");
            
            // Workflow info
            $this->table(
                ['Property', 'Value'],
                [
                    ['Name', $workflow->name],
                    ['Version', "v{$workflow->version}"],
                    ['Status', ucfirst($workflow->status)],
                    ['Description', $workflow->description ?? 'N/A'],
                    ['Created', $workflow->created_at->format('Y-m-d H:i:s')],
                ]
            );

            // Statistics
            $this->info("\nExecution Statistics:");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Instances', $statistics['total_instances']],
                    ['Running Instances', $statistics['running_instances']],
                    ['Completed Instances', $statistics['completed_instances']],
                    ['Failed Instances', $statistics['failed_instances']],
                    ['Success Rate', $this->calculateSuccessRate($statistics) . '%'],
                    ['Average Duration', isset($statistics['average_duration']) ? round($statistics['average_duration'], 2) . 's' : 'N/A'],
                ]
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Show global statistics
     */
    private function showGlobalStatistics(SharWorkflowService $sharService, bool $detailed, string $format): int
    {
        $globalStats = $sharService->getWorkflowStatistics();
        $workflows = $sharService->getWorkflows();

        if ($format === 'json') {
            $data = [
                'global_statistics' => $globalStats,
                'workflow_count' => $workflows->count(),
                'workflows' => $detailed ? $workflows->map(function ($workflow) use ($sharService) {
                    return [
                        'name' => $workflow->name,
                        'statistics' => $sharService->getWorkflowStatistics($workflow->name),
                    ];
                })->toArray() : null,
            ];
            
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
        } else {
            $this->info("SHAR Global Statistics:");
            
            // Global statistics
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Workflows', $workflows->count()],
                    ['Active Workflows', $workflows->where('status', 'active')->count()],
                    ['Total Instances', $globalStats['total_instances']],
                    ['Running Instances', $globalStats['running_instances']],
                    ['Completed Instances', $globalStats['completed_instances']],
                    ['Failed Instances', $globalStats['failed_instances']],
                    ['Overall Success Rate', $this->calculateSuccessRate($globalStats) . '%'],
                    ['Average Duration', isset($globalStats['average_duration']) ? round($globalStats['average_duration'], 2) . 's' : 'N/A'],
                ]
            );

            // Detailed workflow breakdown
            if ($detailed && $workflows->isNotEmpty()) {
                $this->info("\nWorkflow Breakdown:");
                
                $workflowData = $workflows->map(function ($workflow) use ($sharService) {
                    $stats = $sharService->getWorkflowStatistics($workflow->name);
                    return [
                        $workflow->name,
                        "v{$workflow->version}",
                        ucfirst($workflow->status),
                        $stats['total_instances'],
                        $stats['running_instances'],
                        $stats['completed_instances'],
                        $stats['failed_instances'],
                        $this->calculateSuccessRate($stats) . '%',
                    ];
                })->toArray();

                $this->table(
                    ['Workflow', 'Version', 'Status', 'Total', 'Running', 'Completed', 'Failed', 'Success %'],
                    $workflowData
                );
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate success rate percentage
     */
    private function calculateSuccessRate(array $statistics): float
    {
        $total = $statistics['total_instances'];
        $completed = $statistics['completed_instances'];
        
        if ($total === 0) {
            return 0;
        }
        
        return round(($completed / $total) * 100, 1);
    }
}