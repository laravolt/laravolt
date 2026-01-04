<?php

declare(strict_types=1);

namespace Laravolt\Thunderclap\Commands;

use Illuminate\Console\Command;
use Laravolt\Thunderclap\ModelDetector;

class ListModelsCommand extends Command
{
    protected $signature = 'laravolt:models {--table= : Show model for specific table}';

    protected $description = 'List all available models and their enhancement status';

    protected $modelDetector;

    public function __construct()
    {
        parent::__construct();
        $this->modelDetector = new ModelDetector;
    }

    public function handle()
    {
        if ($table = $this->option('table')) {
            $this->showModelForTable($table);

            return;
        }

        $this->showAllModels();
    }

    protected function showModelForTable(string $table)
    {
        $this->info("Checking model for table: {$table}");

        $existingModel = $this->modelDetector->detectExistingModel($table);

        if ($existingModel) {
            $this->showModelDetails($existingModel);
        } else {
            $suggestedName = $this->modelDetector->suggestModelName($table);
            $this->warn("No existing model found for table '{$table}'");
            $this->line("Suggested model name: <fg=yellow>{$suggestedName}</fg=yellow>");
        }
    }

    protected function showAllModels()
    {
        $this->info('Scanning app/Models directory...');

        $models = $this->modelDetector->getAllModels();

        if (empty($models)) {
            $this->warn('No models found in app/Models directory');

            return;
        }

        $this->info('Found '.count($models).' model(s):');
        $this->newLine();

        $headers = ['Model', 'Class', 'Table', 'Auto Traits', 'Searchable'];
        $rows = [];

        foreach ($models as $model) {
            $enhancement = $this->modelDetector->needsEnhancement($model['class']);
            $table = $this->modelDetector->getTableFromModel($model['class']) ?: 'N/A';

            $autoTraits = $enhancement['needs_enhancement'] ? '❌' : '✅';
            $searchable = $enhancement['has_searchable_columns'] ? '✅' : '❌';

            $rows[] = [
                $model['name'],
                $model['class'],
                $table,
                $autoTraits,
                $searchable,
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->line('<fg=cyan>Legend:</fg=cyan>');
        $this->line('  Auto Traits: AutoFilter, AutoSearch, AutoSort');
        $this->line('  Searchable: Has $searchableColumns property');
    }

    protected function showModelDetails(array $model)
    {
        $this->line("<fg=green>✓ Model found:</fg=green> {$model['class']}");
        $this->line("  Path: {$model['path']}");

        $table = $this->modelDetector->getTableFromModel($model['class']);
        if ($table) {
            $this->line("  Table: {$table}");
        }

        $enhancement = $this->modelDetector->needsEnhancement($model['class']);

        $this->newLine();
        $this->line('<fg=cyan>Enhancement Status:</fg=cyan>');

        if ($enhancement['needs_enhancement']) {
            $this->line('  <fg=yellow>Missing traits:</fg=yellow>');
            foreach ($enhancement['missing_traits'] as $trait) {
                $this->line('    - '.class_basename($trait));
            }
        } else {
            $this->line('  <fg=green>✓ All required traits present</fg=green>');
        }

        if ($enhancement['has_searchable_columns']) {
            $this->line('  <fg=green>✓ Has searchableColumns property</fg=green>');
        } else {
            $this->line('  <fg=yellow>❌ Missing searchableColumns property</fg=yellow>');
        }

        if ($enhancement['needs_enhancement'] || ! $enhancement['has_searchable_columns']) {
            $this->newLine();
            $this->line('<fg=cyan>To enhance this model, run:</fg=cyan>');
            $this->line("  php artisan laravolt:clap --table={$table} --use-existing-models");
        }
    }
}
