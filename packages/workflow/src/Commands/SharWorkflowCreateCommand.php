<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharWorkflowCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:workflow:create 
                            {name : The workflow name}
                            {file : Path to the BPMN file}
                            {--description= : Workflow description}
                            {--user-id= : User ID who creates the workflow}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new SHAR workflow from a BPMN file';

    /**
     * Execute the console command.
     */
    public function handle(SharWorkflowService $sharService): int
    {
        $name = $this->argument('name');
        $filePath = $this->argument('file');
        $description = $this->option('description');
        $userId = $this->option('user-id');

        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("BPMN file not found: {$filePath}");
            return Command::FAILURE;
        }

        // Read BPMN content
        $bpmnXml = File::get($filePath);

        // Validate BPMN content
        if (empty($bpmnXml) || !str_contains($bpmnXml, '<bpmn:definitions')) {
            $this->error('Invalid BPMN file format');
            return Command::FAILURE;
        }

        $this->info("Creating workflow '{$name}' from file: {$filePath}");

        try {
            $workflow = $sharService->createWorkflow(
                $name,
                $bpmnXml,
                $description,
                $userId ? (int) $userId : null
            );

            $this->info("✅ Workflow created successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['ID', $workflow->id],
                    ['Name', $workflow->name],
                    ['Description', $workflow->description ?? 'N/A'],
                    ['Version', $workflow->version],
                    ['Status', $workflow->status],
                    ['Created At', $workflow->created_at->format('Y-m-d H:i:s')],
                ]
            );

            return Command::SUCCESS;

        } catch (SharException $e) {
            $this->error("Failed to create workflow: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}