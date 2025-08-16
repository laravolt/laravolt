<?php

namespace Laravolt\SemanticForm\Console;

use Illuminate\Console\Command;
use Laravolt\SemanticForm\UIManager;

class UIFrameworkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ui:framework 
                            {action : The action to perform (list, switch, info, detect, status)}
                            {framework? : The UI framework (semantic, preline)}
                            {--enable : Enable the specified framework}
                            {--disable : Disable the specified framework}
                            {--auto-detect : Enable auto-detection}
                            {--publish : Publish UI configuration files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage UI frameworks (Semantic UI, Preline UI)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $framework = $this->argument('framework');

        if ($this->option('publish')) {
            $this->call('vendor:publish', ['--tag' => 'ui-config']);
            return 0;
        }

        switch ($action) {
            case 'list':
                return $this->listUIFrameworks();
            
            case 'switch':
                return $this->switchUIFramework($framework);
            
            case 'info':
                return $this->showFrameworkInfo($framework);
            
            case 'detect':
                return $this->autoDetectFramework();
            
            case 'status':
                return $this->showFrameworkStatus();
            
            default:
                $this->error("Unknown action: {$action}");
                $this->line('Available actions: list, switch, info, detect, status');
                return 1;
        }
    }

    /**
     * List all available UI frameworks.
     *
     * @return int
     */
    protected function listUIFrameworks()
    {
        $uiManager = app(UIManager::class);
        $frameworks = $uiManager->getAvailableFrameworks();
        $current = $uiManager->getCurrentFramework();

        $this->info('Available UI Frameworks:');
        $this->line('');

        $headers = ['Framework', 'Name', 'CSS Framework', 'JS Framework', 'Form Builder', 'Status', 'Current'];
        $rows = [];

        foreach ($frameworks as $framework) {
            $info = $uiManager->getFrameworkInfo($framework);
            $status = $info['enabled'] ? '✓ Enabled' : '✗ Disabled';
            $isCurrent = $info['is_current'] ? '✓' : '';
            
            $rows[] = [
                $framework,
                $info['name'],
                $info['css_framework'],
                $info['js_framework'],
                $info['form_builder'],
                $status,
                $isCurrent
            ];
        }

        $this->table($headers, $rows);

        $this->line('');
        $this->info("Current framework: {$current}");
        $this->line("Default framework: " . $uiManager->getDefaultFramework());

        return 0;
    }

    /**
     * Switch to a different UI framework.
     *
     * @param string|null $framework
     * @return int
     */
    protected function switchUIFramework($framework)
    {
        if (!$framework) {
            $this->error('Please specify a UI framework.');
            $uiManager = app(UIManager::class);
            $this->line('Available frameworks: ' . implode(', ', $uiManager->getAvailableFrameworks()));
            return 1;
        }

        $uiManager = app(UIManager::class);
        $available = $uiManager->getAvailableFrameworks();

        if (!in_array($framework, $available)) {
            $this->error("Unknown UI framework: {$framework}");
            $this->line('Available frameworks: ' . implode(', ', $available));
            return 1;
        }

        if (!$uiManager->isFrameworkEnabled($framework)) {
            $this->error("UI framework [{$framework}] is not enabled.");
            
            if ($this->option('enable')) {
                $this->enableFramework($framework);
            } else {
                $this->line("Use --enable flag to enable it: php artisan ui:framework switch {$framework} --enable");
                return 1;
            }
        }

        // Update the .env file
        $this->updateEnvironmentFile('UI_FRAMEWORK', $framework);
        
        // Also update form builder if configured
        $formBuilder = $uiManager->getFrameworkInfo($framework)['form_builder'];
        if ($formBuilder) {
            $this->updateEnvironmentFile('FORM_BUILDER', $formBuilder);
        }

        $this->info("Switched to {$framework} UI framework.");
        $this->warn("Please clear your config cache with: php artisan config:clear");

        return 0;
    }

    /**
     * Show information about a UI framework.
     *
     * @param string|null $framework
     * @return int
     */
    protected function showFrameworkInfo($framework)
    {
        $uiManager = app(UIManager::class);
        $framework = $framework ?: $uiManager->getCurrentFramework();
        
        try {
            $info = $uiManager->getFrameworkInfo($framework);
            
            $this->info("UI Framework Information: {$framework}");
            $this->line('');
            
            $details = [
                ['Property', 'Value'],
                ['Name', $info['name']],
                ['Framework', $info['framework']],
                ['CSS Framework', $info['css_framework']],
                ['JS Framework', $info['js_framework']],
                ['Form Builder', $info['form_builder']],
                ['Enabled', $info['enabled'] ? 'Yes' : 'No'],
                ['Is Current', $info['is_current'] ? 'Yes' : 'No'],
            ];
            
            $this->table($details[0], array_slice($details, 1));

            // Show settings
            if (!empty($info['settings'])) {
                $this->line('');
                $this->info('Framework Settings:');
                
                $settingsTable = [['Setting', 'Value']];
                foreach ($info['settings'] as $key => $value) {
                    if (is_array($value)) {
                        $value = json_encode($value, JSON_PRETTY_PRINT);
                    }
                    $settingsTable[] = [$key, $value];
                }
                
                $this->table($settingsTable[0], array_slice($settingsTable, 1));
            }
            
        } catch (\Exception $e) {
            $this->error("Error getting info for {$framework}: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Auto-detect the best UI framework.
     *
     * @return int
     */
    protected function autoDetectFramework()
    {
        $uiManager = app(UIManager::class);
        
        $this->info('Running UI framework auto-detection...');
        $this->line('');

        $detected = $uiManager->autoDetect();
        $current = $uiManager->getCurrentFramework();

        $this->info("Detection results:");
        $this->line("Current framework: {$current}");
        $this->line("Detected best framework: {$detected}");

        if ($detected !== $current) {
            if ($this->confirm("Would you like to switch to {$detected}?")) {
                return $this->switchUIFramework($detected);
            }
        } else {
            $this->info("You're already using the recommended UI framework!");
        }

        return 0;
    }

    /**
     * Show framework status and statistics.
     *
     * @return int
     */
    protected function showFrameworkStatus()
    {
        $uiManager = app(UIManager::class);
        
        $this->info('UI Framework Status:');
        $this->line('');

        // Current status
        $current = $uiManager->getCurrentFramework();
        $info = $uiManager->getFrameworkInfo($current);
        
        $this->line("Current Framework: {$info['name']} ({$current})");
        $this->line("CSS Framework: {$info['css_framework']}");
        $this->line("Form Builder: {$info['form_builder']}");
        $this->line('');

        // Available frameworks
        $available = $uiManager->getAvailableFrameworks();
        $enabled = $uiManager->getEnabledFrameworks();
        
        $this->line("Available Frameworks: " . implode(', ', $available));
        $this->line("Enabled Frameworks: " . implode(', ', $enabled));
        $this->line('');

        // Performance stats
        $stats = $uiManager->getPerformanceStats();
        $this->info('Performance Statistics:');
        
        $statsTable = [
            ['Metric', 'Value'],
            ['Cached CSS Mappings', $stats['cached_css_mappings']],
            ['Detection Cache Hits', $stats['detection_cache_hits']],
            ['Config Size', $stats['compiled_config_size']],
            ['Memory Usage', $this->formatBytes($stats['memory_usage'])],
        ];
        
        $this->table($statsTable[0], array_slice($statsTable, 1));

        return 0;
    }

    /**
     * Enable a framework.
     *
     * @param string $framework
     * @return void
     */
    protected function enableFramework($framework)
    {
        $envKey = strtoupper($framework) . '_UI_ENABLED';
        $this->updateEnvironmentFile($envKey, 'true');
        $this->info("Enabled {$framework} UI framework.");
    }

    /**
     * Update environment file.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function updateEnvironmentFile($key, $value)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            $this->warn(".env file not found. Please manually set {$key}={$value}");
            return;
        }

        $envContent = file_get_contents($envPath);
        
        if (preg_match("/^{$key}=.*$/m", $envContent)) {
        $escapedKey = preg_quote($key, '/');
        if (preg_match("/^{$escapedKey}=.*$/m", $envContent)) {
            $envContent = preg_replace("/^{$escapedKey}=.*$/m", "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}\n";
        }
        
        file_put_contents($envPath, $envContent);
    }

    /**
     * Format bytes into human readable format.
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}