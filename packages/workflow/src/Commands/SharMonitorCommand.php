<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\Models\SharWorkflowInstance;
use Laravolt\Workflow\SharWorkflowService;

class SharMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:monitor 
                            {--workflow= : Monitor specific workflow}
                            {--interval=10 : Refresh interval in seconds}
                            {--limit=20 : Limit number of instances to show}
                            {--auto-sync : Automatically sync instances with SHAR}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor SHAR workflow instances in real-time';

    private array $previousStats = [];

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $workflowName = $this->option('workflow');
        $interval = (int) $this->option('interval');
        $limit = (int) $this->option('limit');
        $autoSync = $this->option('auto-sync');

        $this->info("🔍 SHAR Workflow Monitor");
        $this->line("Workflow: " . ($workflowName ?? 'All'));
        $this->line("Refresh interval: {$interval}s");
        $this->line("Instance limit: {$limit}");
        $this->line("Auto-sync: " . ($autoSync ? 'Yes' : 'No'));
        $this->line("Press Ctrl+C to stop monitoring");
        $this->newLine();

        $iteration = 0;

        while (true) {
            $iteration++;
            
            try {
                $this->clearScreen();
                $this->displayHeader($iteration);
                $this->displayHealthStatus($sharService);
                $this->displayStatistics($sharService, $workflowName);
                $this->displayRecentInstances($sharService, $workflowName, $limit, $autoSync);
                
                $this->line("\n" . str_repeat('=', 80));
                $this->line("Last updated: " . now()->format('Y-m-d H:i:s') . " | Next refresh in {$interval}s");

            } catch (SharException $e) {
                $this->error("Monitor error: {$e->getMessage()}");
            }

            sleep($interval);
        }

        return Command::SUCCESS;
    }

    /**
     * Clear screen for better display
     */
    private function clearScreen(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            system('cls');
        } else {
            system('clear');
        }
    }

    /**
     * Display header
     */
    private function displayHeader(int $iteration): void
    {
        $this->line("╔" . str_repeat('═', 78) . "╗");
        $this->line("║" . str_pad(" SHAR Workflow Monitor - Iteration #{$iteration}", 78) . "║");
        $this->line("╚" . str_repeat('═', 78) . "╝");
        $this->newLine();
    }

    /**
     * Display health status
     */
    private function displayHealthStatus(SharWorkflowService $sharService): void
    {
        try {
            $health = $sharService->checkHealth();
            $isHealthy = $health['status'] === 'healthy';
            $statusIcon = $isHealthy ? '✅' : '❌';
            $statusText = ucfirst($health['status']);
            
            $this->line("🏥 Health Status: {$statusIcon} {$statusText}");
            
            if (!$isHealthy && isset($health['error'])) {
                $this->line("   Error: {$health['error']}");
            }
        } catch (SharException $e) {
            $this->line("🏥 Health Status: ❌ Connection Failed");
            $this->line("   Error: {$e->getMessage()}");
        }
        
        $this->newLine();
    }

    /**
     * Display statistics with changes
     */
    private function displayStatistics(SharWorkflowService $sharService, ?string $workflowName): void
    {
        try {
            $stats = $sharService->getWorkflowStatistics($workflowName);
            $title = $workflowName ? "📊 Statistics for '{$workflowName}'" : "📊 Global Statistics";
            
            $this->line($title);
            
            $this->displayStatLine('Total Instances', $stats['total_instances']);
            $this->displayStatLine('Running', $stats['running_instances'], 'running');
            $this->displayStatLine('Completed', $stats['completed_instances'], 'completed');
            $this->displayStatLine('Failed', $stats['failed_instances'], 'failed');
            
            if (isset($stats['average_duration'])) {
                $avgDuration = round($stats['average_duration'], 1);
                $this->line("   Average Duration: {$avgDuration}s");
            }

            $this->previousStats = $stats;
            
        } catch (SharException $e) {
            $this->line("📊 Statistics: ❌ Failed to load");
        }
        
        $this->newLine();
    }

    /**
     * Display a statistic line with change indicator
     */
    private function displayStatLine(string $label, int $current, string $key = null): void
    {
        $change = '';
        $actualKey = $key ?? strtolower(str_replace(' ', '_', $label));
        
        if (isset($this->previousStats[$actualKey])) {
            $previous = $this->previousStats[$actualKey];
            $diff = $current - $previous;
            
            if ($diff > 0) {
                $change = " (↑+{$diff})";
            } elseif ($diff < 0) {
                $change = " (↓{$diff})";
            }
        }
        
        $this->line("   {$label}: {$current}{$change}");
    }

    /**
     * Display recent instances
     */
    private function displayRecentInstances(
        SharWorkflowService $sharService, 
        ?string $workflowName, 
        int $limit, 
        bool $autoSync
    ): void {
        $this->line("🕒 Recent Instances (last {$limit}):");
        
        try {
            $instances = $sharService->getWorkflowInstances($workflowName);
            $recentInstances = $instances->take($limit);
            
            if ($recentInstances->isEmpty()) {
                $this->line("   No instances found");
                return;
            }

            $tableData = [];
            
            foreach ($recentInstances as $instance) {
                // Auto-sync if enabled and instance is running
                if ($autoSync && $instance->isRunning()) {
                    try {
                        $instance = $sharService->syncWorkflowInstance($instance->id);
                    } catch (SharException $e) {
                        // Ignore sync errors in monitor mode
                    }
                }

                $duration = $instance->getDurationInSeconds();
                $durationStr = $duration ? "{$duration}s" : 'N/A';
                
                $statusIcon = $this->getStatusIcon($instance->status);
                
                $tableData[] = [
                    \Str::limit($instance->id, 12),
                    $instance->workflow_name,
                    "{$statusIcon} " . ucfirst($instance->status),
                    $instance->started_at ? $instance->started_at->format('H:i:s') : 'N/A',
                    $durationStr,
                    count($instance->variables ?? []),
                ];
            }

            $this->table(
                ['Instance ID', 'Workflow', 'Status', 'Started', 'Duration', 'Vars'],
                $tableData
            );
            
        } catch (SharException $e) {
            $this->line("   ❌ Failed to load instances: {$e->getMessage()}");
        }
    }

    /**
     * Get status icon
     */
    private function getStatusIcon(string $status): string
    {
        return match ($status) {
            'running' => '🔄',
            'completed' => '✅',
            'failed' => '❌',
            'cancelled' => '⏹️',
            default => '❓',
        };
    }
}