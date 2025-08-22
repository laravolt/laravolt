<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:health 
                            {--watch : Continuously monitor health}
                            {--interval=30 : Watch interval in seconds}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     */
    protected $description = 'Check SHAR server health and connectivity';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $watch = $this->option('watch');
        $interval = (int) $this->option('interval');
        $format = $this->option('format');

        if ($watch) {
            return $this->watchHealth($sharService, $interval, $format);
        } else {
            return $this->checkHealthOnce($sharService, $format);
        }
    }

    /**
     * Check health once
     */
    private function checkHealthOnce(SharWorkflowService $sharService, string $format): int
    {
        $this->info('Checking SHAR server health...');

        try {
            $health = $sharService->checkHealth();
            $statistics = $sharService->getWorkflowStatistics();

            if ($format === 'json') {
                $data = [
                    'health' => $health,
                    'statistics' => $statistics,
                    'configuration' => [
                        'base_url' => config('workflow.shar.base_url'),
                        'timeout' => config('workflow.shar.timeout'),
                        'enabled' => config('workflow.shar.enabled'),
                    ],
                ];
                
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            } else {
                // Health status
                $isHealthy = $health['status'] === 'healthy';
                $statusIcon = $isHealthy ? '✅' : '❌';
                $statusColor = $isHealthy ? 'info' : 'error';
                
                $this->$statusColor("{$statusIcon} SHAR Server: " . ucfirst($health['status']));

                if (!$isHealthy && isset($health['error'])) {
                    $this->error("Error: {$health['error']}");
                }

                // Configuration
                $this->info("\nConfiguration:");
                $this->table(
                    ['Setting', 'Value'],
                    [
                        ['Base URL', config('workflow.shar.base_url')],
                        ['Timeout', config('workflow.shar.timeout') . 's'],
                        ['Enabled', config('workflow.shar.enabled') ? 'Yes' : 'No'],
                        ['NATS URL', config('workflow.shar.nats_url')],
                        ['Log Level', config('workflow.shar.log_level')],
                    ]
                );

                // Statistics
                if ($isHealthy) {
                    $this->info("\nWorkflow Statistics:");
                    $this->table(
                        ['Metric', 'Count'],
                        [
                            ['Total Instances', $statistics['total_instances']],
                            ['Running Instances', $statistics['running_instances']],
                            ['Completed Instances', $statistics['completed_instances']],
                            ['Failed Instances', $statistics['failed_instances']],
                            ['Average Duration', isset($statistics['average_duration']) ? round($statistics['average_duration'], 2) . 's' : 'N/A'],
                        ]
                    );
                }
            }

            return $isHealthy ? Command::SUCCESS : Command::FAILURE;

        } catch (SharException $e) {
            $this->error("Health check failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Continuously watch health
     */
    private function watchHealth(SharWorkflowService $sharService, int $interval, string $format): int
    {
        $this->info("Watching SHAR health (interval: {$interval}s). Press Ctrl+C to stop.");
        $this->newLine();

        $iteration = 0;
        
        while (true) {
            $iteration++;
            $timestamp = now()->format('Y-m-d H:i:s');
            
            if ($format === 'table') {
                $this->line("=== Health Check #{$iteration} - {$timestamp} ===");
            }

            try {
                $health = $sharService->checkHealth();
                $isHealthy = $health['status'] === 'healthy';

                if ($format === 'json') {
                    $data = [
                        'timestamp' => $timestamp,
                        'iteration' => $iteration,
                        'health' => $health,
                    ];
                    $this->line(json_encode($data));
                } else {
                    $statusIcon = $isHealthy ? '✅' : '❌';
                    $this->line("{$statusIcon} Status: " . ucfirst($health['status']));
                    
                    if (!$isHealthy && isset($health['error'])) {
                        $this->line("❌ Error: {$health['error']}");
                    }
                }

            } catch (SharException $e) {
                if ($format === 'json') {
                    $data = [
                        'timestamp' => $timestamp,
                        'iteration' => $iteration,
                        'error' => $e->getMessage(),
                    ];
                    $this->line(json_encode($data));
                } else {
                    $this->line("❌ Connection failed: {$e->getMessage()}");
                }
            }

            if ($format === 'table') {
                $this->newLine();
            }

            sleep($interval);
        }

        return Command::SUCCESS;
    }
}