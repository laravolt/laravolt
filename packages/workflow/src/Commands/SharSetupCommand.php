<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Laravolt\Workflow\Exceptions\SharException;
use Laravolt\Workflow\SharWorkflowService;

class SharSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:setup 
                            {--skip-migrations : Skip running migrations}
                            {--skip-docker : Skip Docker setup check}
                            {--example : Create example workflow}';

    /**
     * The console command description.
     */
    protected $description = 'Setup and configure SHAR integration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🚀 Setting up SHAR integration...");
        $this->newLine();

        // Check configuration
        $this->checkConfiguration();

        // Run migrations unless skipped
        if (!$this->option('skip-migrations')) {
            $this->runMigrations();
        }

        // Check Docker setup unless skipped
        if (!$this->option('skip-docker')) {
            $this->checkDockerSetup();
        }

        // Check SHAR connectivity
        $this->checkSharConnectivity();

        // Create example workflow if requested
        if ($this->option('example')) {
            $this->createExampleWorkflow();
        }

        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Check configuration
     */
    private function checkConfiguration(): void
    {
        $this->info("📋 Checking configuration...");

        $config = [
            'SHAR_ENABLED' => config('workflow.shar.enabled'),
            'SHAR_BASE_URL' => config('workflow.shar.base_url'),
            'SHAR_TIMEOUT' => config('workflow.shar.timeout'),
            'NATS_URL' => config('workflow.shar.nats_url'),
            'SHAR_LOG_LEVEL' => config('workflow.shar.log_level'),
        ];

        $this->table(
            ['Configuration', 'Value', 'Status'],
            collect($config)->map(function ($value, $key) {
                $status = $value ? '✅' : '❌';
                $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
                return [$key, $displayValue, $status];
            })->toArray()
        );

        if (!config('workflow.shar.enabled')) {
            $this->warn("⚠️  SHAR is disabled. Set SHAR_ENABLED=true in your .env file");
        }

        $this->newLine();
    }

    /**
     * Run database migrations
     */
    private function runMigrations(): void
    {
        $this->info("🗄️  Running database migrations...");

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->info("✅ Migrations completed successfully");
        } catch (\Exception $e) {
            $this->error("❌ Migration failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Check Docker setup
     */
    private function checkDockerSetup(): void
    {
        $this->info("🐳 Checking Docker setup...");

        // Check if Docker is installed
        $dockerInstalled = $this->checkCommand('docker --version');
        $dockerComposeInstalled = $this->checkCommand('docker-compose --version');

        $this->table(
            ['Component', 'Status'],
            [
                ['Docker', $dockerInstalled ? '✅ Installed' : '❌ Not found'],
                ['Docker Compose', $dockerComposeInstalled ? '✅ Installed' : '❌ Not found'],
            ]
        );

        if ($dockerInstalled && $dockerComposeInstalled) {
            // Check if SHAR services are running
            $sharPath = base_path('packages/shar');
            if (File::exists($sharPath . '/docker-compose.yml')) {
                $this->info("Docker Compose file found at: {$sharPath}/docker-compose.yml");
                
                if ($this->confirm('Start SHAR services with Docker Compose?')) {
                    $this->info('Starting SHAR services...');
                    
                    $exitCode = 0;
                    $output = [];
                    exec("cd {$sharPath} && docker-compose up -d", $output, $exitCode);
                    
                    if ($exitCode === 0) {
                        $this->info("✅ SHAR services started successfully");
                        sleep(5); // Wait for services to be ready
                    } else {
                        $this->error("❌ Failed to start SHAR services");
                        $this->line(implode("\n", $output));
                    }
                }
            } else {
                $this->warn("⚠️  Docker Compose file not found at: {$sharPath}");
            }
        }

        $this->newLine();
    }

    /**
     * Check SHAR connectivity
     */
    private function checkSharConnectivity(): void
    {
        $this->info("🔗 Checking SHAR connectivity...");

        try {
            $sharService = app(SharWorkflowService::class);
            $health = $sharService->checkHealth();
            
            $isHealthy = $health['status'] === 'healthy';
            $statusIcon = $isHealthy ? '✅' : '❌';
            
            $this->line("SHAR Server: {$statusIcon} " . ucfirst($health['status']));
            
            if ($isHealthy) {
                $stats = $sharService->getWorkflowStatistics();
                $this->line("Total Workflows: {$stats['total_instances']}");
                $this->line("Running Instances: {$stats['running_instances']}");
            } else {
                if (isset($health['error'])) {
                    $this->error("Error: {$health['error']}");
                }
                $this->warn("💡 Try starting SHAR services: cd packages/shar && docker-compose up -d");
            }

        } catch (SharException $e) {
            $this->error("❌ SHAR connectivity failed: {$e->getMessage()}");
            $this->warn("💡 Ensure SHAR server is running at: " . config('workflow.shar.base_url'));
        }

        $this->newLine();
    }

    /**
     * Create example workflow
     */
    private function createExampleWorkflow(): void
    {
        $this->info("📝 Creating example workflow...");

        $examplePath = base_path('packages/shar/examples/simple-workflow.bpmn');
        
        if (!File::exists($examplePath)) {
            $this->warn("Example BPMN file not found at: {$examplePath}");
            return;
        }

        try {
            $sharService = app(SharWorkflowService::class);
            $bpmnXml = File::get($examplePath);
            
            $workflow = $sharService->createWorkflow(
                'ExampleWorkflow',
                $bpmnXml,
                'Example workflow created by setup command'
            );

            $this->info("✅ Example workflow created: {$workflow->name} (v{$workflow->version})");

        } catch (SharException $e) {
            $this->error("❌ Failed to create example workflow: {$e->getMessage()}");
        }
    }

    /**
     * Display setup summary
     */
    private function displaySummary(): void
    {
        $this->info("🎉 SHAR setup completed!");
        $this->newLine();

        $this->line("Next steps:");
        $this->line("1. 📊 Check status: php artisan shar:health");
        $this->line("2. 📝 List workflows: php artisan shar:workflow:list");
        $this->line("3. 🚀 Launch instance: php artisan shar:workflow:launch WorkflowName");
        $this->line("4. 🔍 Monitor: php artisan shar:monitor");
        $this->line("5. 🌐 Web interface: /workflow/shar");
        $this->newLine();

        $this->line("Documentation:");
        $this->line("- SHAR service: packages/shar/README.md");
        $this->line("- Laravel integration: packages/workflow/README-SHAR.md");
    }

    /**
     * Check if a command exists
     */
    private function checkCommand(string $command): bool
    {
        $output = null;
        $exitCode = null;
        exec($command . ' 2>/dev/null', $output, $exitCode);
        return $exitCode === 0;
    }
}