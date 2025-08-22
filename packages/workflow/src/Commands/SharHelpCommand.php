<?php

namespace Laravolt\Workflow\Commands;

use Illuminate\Console\Command;

class SharHelpCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'shar:help {--command= : Show help for specific command}';

    /**
     * The console command description.
     */
    protected $description = 'Show help for SHAR commands';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $specificCommand = $this->option('command');

        if ($specificCommand) {
            return $this->showSpecificHelp($specificCommand);
        }

        $this->showGeneralHelp();
        return Command::SUCCESS;
    }

    /**
     * Show general help for all SHAR commands
     */
    private function showGeneralHelp(): void
    {
        $this->line("╔" . str_repeat('═', 78) . "╗");
        $this->line("║" . str_pad(" SHAR (Simple Hyperscale Activity Router) Commands", 78) . "║");
        $this->line("╚" . str_repeat('═', 78) . "╝");
        $this->newLine();

        $this->info("🛠️  Setup & Configuration:");
        $this->line("  shar:setup              Setup and configure SHAR integration");
        $this->line("  shar:health             Check SHAR server health and connectivity");
        $this->newLine();

        $this->info("📊 Monitoring & Statistics:");
        $this->line("  shar:monitor            Monitor workflow instances in real-time");
        $this->line("  shar:statistics         Show workflow statistics and analytics");
        $this->newLine();

        $this->info("📝 Workflow Management:");
        $this->line("  shar:workflow:create    Create workflow from BPMN file");
        $this->line("  shar:workflow:list      List all workflows");
        $this->line("  shar:workflow:launch    Launch workflow instance");
        $this->line("  shar:workflow:delete    Delete workflow");
        $this->line("  shar:workflow:export    Export workflow to BPMN file");
        $this->newLine();

        $this->info("🔄 Instance Management:");
        $this->line("  shar:instance:list      List workflow instances");
        $this->line("  shar:instance:show      Show instance details");
        $this->line("  shar:instance:complete  Complete workflow instance");
        $this->newLine();

        $this->info("🔧 Maintenance & Operations:");
        $this->line("  shar:sync               Sync instances with SHAR server");
        $this->line("  shar:cleanup            Cleanup old workflow instances");
        $this->line("  shar:batch              Perform batch operations");
        $this->newLine();

        $this->info("💡 Quick Examples:");
        $this->line("  # Setup SHAR integration");
        $this->line("  php artisan shar:setup --example");
        $this->newLine();
        
        $this->line("  # Create workflow from BPMN file");
        $this->line("  php artisan shar:workflow:create MyWorkflow /path/to/workflow.bpmn");
        $this->newLine();
        
        $this->line("  # Launch workflow with variables");
        $this->line("  php artisan shar:workflow:launch MyWorkflow --variables='{\"key\":\"value\"}'");
        $this->newLine();
        
        $this->line("  # Monitor workflows in real-time");
        $this->line("  php artisan shar:monitor --interval=5");
        $this->newLine();
        
        $this->line("  # Batch launch 10 instances");
        $this->line("  php artisan shar:batch launch --workflow=MyWorkflow --count=10");
        $this->newLine();

        $this->info("📚 For detailed help on any command:");
        $this->line("  php artisan shar:help --command=COMMAND_NAME");
        $this->line("  php artisan help COMMAND_NAME");
    }

    /**
     * Show help for a specific command
     */
    private function showSpecificHelp(string $command): int
    {
        $helpTexts = [
            'setup' => $this->getSetupHelp(),
            'health' => $this->getHealthHelp(),
            'monitor' => $this->getMonitorHelp(),
            'statistics' => $this->getStatisticsHelp(),
            'workflow:create' => $this->getWorkflowCreateHelp(),
            'workflow:list' => $this->getWorkflowListHelp(),
            'workflow:launch' => $this->getWorkflowLaunchHelp(),
            'workflow:delete' => $this->getWorkflowDeleteHelp(),
            'workflow:export' => $this->getWorkflowExportHelp(),
            'instance:list' => $this->getInstanceListHelp(),
            'instance:show' => $this->getInstanceShowHelp(),
            'instance:complete' => $this->getInstanceCompleteHelp(),
            'sync' => $this->getSyncHelp(),
            'cleanup' => $this->getCleanupHelp(),
            'batch' => $this->getBatchHelp(),
        ];

        if (!isset($helpTexts[$command])) {
            $this->error("Unknown command: {$command}");
            $this->line("Available commands: " . implode(', ', array_keys($helpTexts)));
            return Command::FAILURE;
        }

        $this->line($helpTexts[$command]);
        return Command::SUCCESS;
    }

    private function getSetupHelp(): string
    {
        return "
🚀 shar:setup - Setup and configure SHAR integration

USAGE:
  php artisan shar:setup [options]

OPTIONS:
  --skip-migrations    Skip running database migrations
  --skip-docker        Skip Docker setup check
  --example           Create example workflow after setup

DESCRIPTION:
  This command sets up SHAR integration by:
  - Checking configuration
  - Running database migrations
  - Verifying Docker setup
  - Testing SHAR connectivity
  - Optionally creating example workflow

EXAMPLES:
  php artisan shar:setup
  php artisan shar:setup --example
  php artisan shar:setup --skip-docker
";
    }

    private function getHealthHelp(): string
    {
        return "
🏥 shar:health - Check SHAR server health and connectivity

USAGE:
  php artisan shar:health [options]

OPTIONS:
  --watch              Continuously monitor health
  --interval=30        Watch interval in seconds
  --format=table       Output format (table, json)

DESCRIPTION:
  Checks SHAR server health, configuration, and displays statistics.

EXAMPLES:
  php artisan shar:health
  php artisan shar:health --watch --interval=10
  php artisan shar:health --format=json
";
    }

    private function getMonitorHelp(): string
    {
        return "
🔍 shar:monitor - Monitor workflow instances in real-time

USAGE:
  php artisan shar:monitor [options]

OPTIONS:
  --workflow=NAME      Monitor specific workflow
  --interval=10        Refresh interval in seconds
  --limit=20          Limit number of instances to show
  --auto-sync         Automatically sync instances with SHAR

DESCRIPTION:
  Real-time monitoring dashboard showing health, statistics, and recent instances.

EXAMPLES:
  php artisan shar:monitor
  php artisan shar:monitor --workflow=MyWorkflow --interval=5
  php artisan shar:monitor --auto-sync --limit=50
";
    }

    private function getBatchHelp(): string
    {
        return "
📦 shar:batch - Perform batch operations

USAGE:
  php artisan shar:batch {operation} [options]

OPERATIONS:
  import              Import workflows from JSON file
  launch              Launch multiple workflow instances
  complete            Complete multiple workflow instances

OPTIONS:
  --file=PATH         JSON file with batch data (for import)
  --workflow=NAME     Workflow name (for launch)
  --instances=IDs     Comma-separated instance IDs (for complete)
  --variables=JSON    JSON variables (for launch)
  --count=N           Number of instances to launch
  --dry-run           Show what would be done
  --force             Force operation without confirmation

EXAMPLES:
  php artisan shar:batch import --file=workflows.json
  php artisan shar:batch launch --workflow=MyWorkflow --count=10
  php artisan shar:batch complete --instances=id1,id2,id3
";
    }

    private function getWorkflowCreateHelp(): string
    {
        return "
📝 shar:workflow:create - Create workflow from BPMN file

USAGE:
  php artisan shar:workflow:create {name} {file} [options]

ARGUMENTS:
  name                Workflow name
  file                Path to BPMN file

OPTIONS:
  --description=TEXT  Workflow description
  --user-id=ID        User ID who creates the workflow

EXAMPLES:
  php artisan shar:workflow:create OrderProcess /path/to/order.bpmn
  php artisan shar:workflow:create OrderProcess order.bpmn --description='Order processing workflow'
";
    }

    private function getWorkflowLaunchHelp(): string
    {
        return "
🚀 shar:workflow:launch - Launch workflow instance

USAGE:
  php artisan shar:workflow:launch {name} [options]

ARGUMENTS:
  name                Workflow name

OPTIONS:
  --variables=JSON    JSON string of variables
  --user-id=ID        User ID who launches the workflow
  --wait              Wait for workflow completion

EXAMPLES:
  php artisan shar:workflow:launch OrderProcess
  php artisan shar:workflow:launch OrderProcess --variables='{\"orderId\":\"123\"}'
  php artisan shar:workflow:launch OrderProcess --wait
";
    }

    // Add more help methods for other commands...
    private function getSyncHelp(): string
    {
        return "
🔄 shar:sync - Sync workflow instances with SHAR server

USAGE:
  php artisan shar:sync [options]

OPTIONS:
  --instance=ID       Sync specific instance
  --workflow=NAME     Sync all instances of workflow
  --status=STATUS     Sync instances with status
  --force             Force sync without confirmation
  --batch-size=N      Batch size for processing

EXAMPLES:
  php artisan shar:sync --instance=abc123
  php artisan shar:sync --workflow=OrderProcess
  php artisan shar:sync --status=running --batch-size=100
";
    }

    private function getCleanupHelp(): string
    {
        return "
🧹 shar:cleanup - Cleanup old workflow instances

USAGE:
  php artisan shar:cleanup [options]

OPTIONS:
  --days=30           Delete instances older than N days
  --status=completed  Status of instances to cleanup
  --dry-run           Show what would be deleted
  --force             Force cleanup without confirmation
  --batch-size=100    Batch size for processing

EXAMPLES:
  php artisan shar:cleanup --days=90 --dry-run
  php artisan shar:cleanup --days=30 --status=failed --force
";
    }
}