<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:workflow:export 
                            {name : The workflow name}
                            {--output= : Output file path (defaults to workflow-name.bpmn)}
                            {--format=bpmn : Export format (bpmn, json)}';

    /**
     * The console command description.
     */
    protected $description = 'Export a SHAR workflow to a BPMN file';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $name = $this->argument('name');
        $outputPath = $this->option('output');
        $format = $this->option('format');

        try {
            $workflow = $sharService->getWorkflow($name);
            
            if (!$workflow) {
                $this->error("Workflow not found: {$name}");
                return Command::FAILURE;
            }

            // Determine output path
            if (!$outputPath) {
                $extension = $format === 'json' ? 'json' : 'bpmn';
                $outputPath = "{$name}.{$extension}";
            }

            // Prepare content based on format
            if ($format === 'json') {
                $content = json_encode([
                    'name' => $workflow->name,
                    'description' => $workflow->description,
                    'version' => $workflow->version,
                    'status' => $workflow->status,
                    'bpmn_xml' => $workflow->bpmn_xml,
                    'created_at' => $workflow->created_at->toISOString(),
                    'statistics' => $workflow->getStatistics(),
                ], JSON_PRETTY_PRINT);
            } else {
                $content = $workflow->bpmn_xml;
            }

            // Check if file already exists
            if (File::exists($outputPath) && !$this->confirm("File '{$outputPath}' already exists. Overwrite?")) {
                $this->info('Export cancelled.');
                return Command::SUCCESS;
            }

            // Write file
            File::put($outputPath, $content);

            $this->info("✅ Workflow exported successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Workflow Name', $workflow->name],
                    ['Version', "v{$workflow->version}"],
                    ['Format', strtoupper($format)],
                    ['Output File', $outputPath],
                    ['File Size', $this->formatBytes(strlen($content))],
                ]
            );

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to export workflow: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}