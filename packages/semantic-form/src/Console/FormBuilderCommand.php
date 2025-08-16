<?php

namespace Laravolt\SemanticForm\Console;

use Illuminate\Console\Command;
use Laravolt\SemanticForm\FormManager;

class FormBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'form:builder 
                            {action : The action to perform (list, switch, info, detect)}
                            {driver? : The form builder driver (semantic, preline)}
                            {--publish : Publish the form configuration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage form builders (SemanticForm, PrelineForm)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $driver = $this->argument('driver');

        if ($this->option('publish')) {
            $this->call('vendor:publish', ['--tag' => 'form-config']);
            return 0;
        }

        switch ($action) {
            case 'list':
                return $this->listFormBuilders();
            
            case 'switch':
                return $this->switchFormBuilder($driver);
            
            case 'info':
                return $this->showBuilderInfo($driver);
            
            case 'detect':
                return $this->autoDetectBuilder();
            
            default:
                $this->error("Unknown action: {$action}");
                $this->line('Available actions: list, switch, info, detect');
                return 1;
        }
    }

    /**
     * List all available form builders.
     *
     * @return int
     */
    protected function listFormBuilders()
    {
        $manager = app(FormManager::class);
        $drivers = $manager->getAvailableDrivers();
        $current = $manager->getCurrentDriver();

        $this->info('Available Form Builders:');
        $this->line('');

        $headers = ['Driver', 'Class', 'UI Framework', 'CSS Framework', 'Status'];
        $rows = [];

        foreach ($drivers as $driver) {
            $info = $manager->getBuilderInfo($driver);
            $status = $info['is_current'] ? '✓ Current' : '';
            
            $rows[] = [
                $driver,
                class_basename($info['class']),
                $info['ui_framework'],
                $info['css_framework'],
                $status
            ];
        }

        $this->table($headers, $rows);

        $this->line('');
        $this->info("Current default: {$current}");

        return 0;
    }

    /**
     * Switch to a different form builder.
     *
     * @param string|null $driver
     * @return int
     */
    protected function switchFormBuilder($driver)
    {
        if (!$driver) {
            $this->error('Please specify a form builder driver.');
            $this->line('Available drivers: ' . implode(', ', app(FormManager::class)->getAvailableDrivers()));
            return 1;
        }

        $manager = app(FormManager::class);
        $available = $manager->getAvailableDrivers();

        if (!in_array($driver, $available)) {
            $this->error("Unknown form builder: {$driver}");
            $this->line('Available drivers: ' . implode(', ', $available));
            return 1;
        }

        // Update the .env file
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            
            if (preg_match('/^FORM_BUILDER=.*$/m', $envContent)) {
                $envContent = preg_replace('/^FORM_BUILDER=.*$/m', "FORM_BUILDER={$driver}", $envContent);
            } else {
                $envContent .= "\nFORM_BUILDER={$driver}\n";
            }
            
            file_put_contents($envPath, $envContent);
            
            $this->info("Switched to {$driver} form builder.");
            $this->line("Updated FORM_BUILDER={$driver} in .env file.");
            $this->warn("Please clear your config cache with: php artisan config:clear");
        } else {
            $this->warn(".env file not found. Please manually set FORM_BUILDER={$driver}");
        }

        return 0;
    }

    /**
     * Show information about a form builder.
     *
     * @param string|null $driver
     * @return int
     */
    protected function showBuilderInfo($driver)
    {
        $manager = app(FormManager::class);
        $driver = $driver ?: $manager->getCurrentDriver();
        
        try {
            $info = $manager->getBuilderInfo($driver);
            
            $this->info("Form Builder Information: {$driver}");
            $this->line('');
            
            $details = [
                ['Property', 'Value'],
                ['Driver', $info['driver']],
                ['Class', $info['class']],
                ['UI Framework', $info['ui_framework']],
                ['CSS Framework', $info['css_framework']],
                ['Description', $info['description']],
                ['Is Current', $info['is_current'] ? 'Yes' : 'No'],
            ];
            
            $this->table($details[0], array_slice($details, 1));
            
        } catch (\Exception $e) {
            $this->error("Error getting info for {$driver}: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Auto-detect the best form builder.
     *
     * @return int
     */
    protected function autoDetectBuilder()
    {
        $manager = app(FormManager::class);
        
        if (!$manager->isAutoDetectionEnabled()) {
            $this->warn('Auto-detection is disabled in configuration.');
            $this->line('Enable it by setting FORM_AUTO_DETECT=true in your .env file.');
            return 1;
        }

        $detected = $manager->autoDetect();
        $current = $manager->getCurrentDriver();

        $this->info("Auto-detection results:");
        $this->line("Current builder: {$current}");
        $this->line("Detected best builder: {$detected}");

        if ($detected !== $current) {
            if ($this->confirm("Would you like to switch to {$detected}?")) {
                return $this->switchFormBuilder($detected);
            }
        } else {
            $this->info("You're already using the recommended form builder!");
        }

        return 0;
    }
}