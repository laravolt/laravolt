<?php

namespace Laravolt\Thunderclap\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravolt\Thunderclap\DBHelper;
use Laravolt\Thunderclap\FileTransformer;
use Laravolt\Thunderclap\ModelDetector;
use Laravolt\Thunderclap\ModelEnhancer;

class Generator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:clap {--table= : Code will be generated based on this table schema}
                    {--template= : Code will be generated based on this stubs structure}
                    {--force : Overwrite files if exists}
                    {--module= : Custom module name you want}
                    {--use-existing-models : Auto-detect and enhance existing models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic CRUD';

    protected $DBHelper;

    protected $packerHelper;

    protected $transformer;

    protected $modelDetector;

    protected $modelEnhancer;

    /**
     * Generator constructor.
     *
     * @return void
     */
    public function __construct(DBHelper $DBHelper, FileTransformer $packerHelper)
    {
        parent::__construct();

        $this->DBHelper = $DBHelper;
        $this->packerHelper = $packerHelper;
        $this->transformer = app(config('laravolt.thunderclap.transformer'));
        $this->modelDetector = new ModelDetector;
        $this->modelEnhancer = new ModelEnhancer($this->transformer);
    }

    public function handle()
    {
        if (($table = $this->option('table')) === null) {
            $tables = $this->DBHelper->listTables();
            $table = $this->choice('Choose table:', $tables, null);
        }

        $columns = collect($this->DBHelper->listColumns($table));
        $this->transformer->setColumns($columns);

        $namespace = config('laravolt.thunderclap.namespace');

        if (($moduleName = $this->option('module')) === null) {
            $moduleName = Str::singular(str_replace('_', '', Str::title($table)));
        }

        // Smart model detection (auto-discovery)
        $existingModel = null;
        $modelAction = 'create'; // default action
        $skipModelGeneration = false;

        // Always check for existing models first
        $existingModel = $this->modelDetector->detectExistingModel($table);

        if ($existingModel) {
            $this->warn("⚠️  Existing model detected: {$existingModel['class']}");

            // If --use-existing-models is explicitly set, auto-enhance
            if ($this->option('use-existing-models')) {
                $modelAction = 'enhance';
                $this->info('🔧 Auto-enhancing existing model...');
                $this->enhanceExistingModel($existingModel, $columns);
                $skipModelGeneration = true;
            } else {
                // Otherwise, show the choice menu
                $choice = $this->choice('How would you like to proceed?', [
                    'enhance' => 'Enhance existing model',
                    'create' => 'Create new model in module',
                    'skip' => 'Skip model generation',
                ], 'enhance');

                $modelAction = $choice;

                if ($choice === 'enhance') {
                    $this->enhanceExistingModel($existingModel, $columns);
                    $skipModelGeneration = true;
                } elseif ($choice === 'skip') {
                    $skipModelGeneration = true;
                }
            }
        }

        $containerPath = config('laravolt.thunderclap.target_dir', base_path('modules'));
        $modulePath = $containerPath.DIRECTORY_SEPARATOR.$moduleName;

        // 1. check existing module
        if (is_dir($modulePath)) {
            $overwrite = $this->option('force') || $this->confirm("Folder {$modulePath} already exist, do you want to overwrite it?");
            if ($overwrite) {
                File::deleteDirectory($modulePath);
            } else {
                return false;
            }
        }

        // 2. create modules directory
        $this->info('Creating modules directory...');
        $this->packerHelper->makeDir($containerPath);
        $this->packerHelper->makeDir($modulePath);

        // 3. copy module skeleton
        $stubs = $this->getStubDir($this->option('template') ?? config('laravolt.thunderclap.default'));
        $this->info(sprintf('Generating code from %s to %s', $stubs, $modulePath));
        File::copyDirectory($stubs, $modulePath);

        $templates = [
            'module-name' => str_replace('_', '-', Str::singular($table)),
            'route-prefix' => config('laravolt.thunderclap.routes.prefix'),
        ];

        // 4. rename file and replace common string
        $replacer = [
            ':Namespace:' => $namespace,
            ':table:' => $table,
            ':ModuleName:' => $moduleName,
            ':Module Name:' => Str::singular(str_replace('_', ' ', Str::title($table))),
            ':module-name:' => $templates['module-name'],
            ':moduleName:' => lcfirst($moduleName),
            ':SEARCHABLE_COLUMNS:' => $this->transformer->toSearchableColumns(),
            ':VALIDATION_RULES:' => $this->transformer->toValidationRules(),
            ':LANG_FIELDS:' => $this->transformer->toLangFields(),
            ':TABLE_HEADERS:' => $this->transformer->toTableHeaders(),
            ':TABLE_FIELDS:' => $this->transformer->toTableFields(),
            ':DETAIL_FIELDS:' => $this->transformer->toDetailFields(lcfirst($moduleName)),
            ':FORM_CREATE_FIELDS:' => $this->transformer->toFormCreateFields(),
            ':FORM_EDIT_FIELDS:' => $this->transformer->toFormEditFields(),
            ':TABLE_VIEW_FIELDS:' => $this->transformer->toTableViewFields(),
            ':VIEW_EXTENDS:' => config('laravolt.thunderclap.view.extends'),
            ':route-prefix:' => $templates['route-prefix'],
            ':route-middleware:' => $this->toArrayElement(config('laravolt.thunderclap.routes.middleware')),
            ':route-url-prefix:' => $this->getRouteUrlPrefix($templates['route-prefix'], $templates['module-name']),
            ':TEST_FACTORY_ATTRIBUTES:' => $this->transformer->toTestFactoryAttributes(),
            ':TEST_UPDATE_ATTRIBUTES:' => $this->transformer->toTestUpdateAttributes(),
        ];

        $isUsingExistingModel = $existingModel && $modelAction === 'enhance';

        // Add model reference for existing models
        if ($isUsingExistingModel) {
            $replacer[':Namespace:\:ModuleName:\Models\:ModuleName:'] = $existingModel['class'];
            $replacer[':MODEL_IMPORT:'] = "use {$existingModel['class']};";
        } else {
            $replacer[':MODEL_IMPORT:'] = "use :Namespace:\:ModuleName:\Models\:ModuleName:;";
        }

        $classToBePrefixed = config('laravolt.thunderclap.prefixed');

        foreach (File::allFiles($modulePath, true) as $file) {
            if (is_file($file)) {
                // Skip model generation if using existing model
                if ($skipModelGeneration && Str::contains($file, '/Models/') && Str::endsWith($file, 'Model.php.stub')) {
                    File::delete($file);

                    continue;
                }

                $newFile = $deleteOriginal = false;

                if (Str::endsWith($file, '.stub')) {
                    $newFile = Str::substr($file, 0, -5);
                    $deleteOriginal = true;
                }

                if (Str::endsWith($newFile, 'Model.php')) {
                    $newFile = Str::replaceLast('Model', $moduleName, $newFile);
                }

                if (Str::endsWith($newFile, 'Test.php')) {
                    $newFile = Str::replaceLast('Test', $moduleName.'Test', $newFile);
                }

                if (Str::endsWith($newFile, 'Factory.php')) {
                    $newFile = Str::replaceLast('Factory', $moduleName.'Factory', $newFile);
                }

                foreach ($classToBePrefixed as $filename) {
                    $class = Str::substr($filename, 0, -4);
                    if (Str::endsWith($newFile, $filename)) {
                        $newFile = Str::replaceLast($class, $moduleName.$class, $newFile);
                    }
                }

                if (Str::endsWith($newFile, 'config.php')) {
                    $newFile = Str::replaceLast('config', $templates['module-name'], $newFile);
                }

                if ($newFile) {
                    $fileNameReplacer = Arr::only($replacer, [
                        ':module_name:',
                        ':module-name:',
                        ':moduleName:',
                        ':ModuleName:',
                    ]);

                    $newFile = str_replace(array_keys($fileNameReplacer), array_values($fileNameReplacer), $newFile);
                }

                if (! $newFile) {
                    continue;
                }

                $this->info($newFile);

                try {
                    $this->packerHelper->replaceAndSave($file, array_keys($replacer), array_values($replacer), $newFile, $deleteOriginal);

                    // Post-process controller files for existing models
                    if ($existingModel && $modelAction === 'enhance' && Str::endsWith($newFile, 'Controller.php')) {
                        $this->postProcessControllerForExistingModel($newFile, $existingModel, $moduleName);
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }

        if (File::exists(base_path('./vendor/bin/pint'))) {
            // 5. Run code style fix
            $this->info('🔁 Running code style fix...');

            $pintCommand = base_path('vendor/bin/pint').' --parallel '.escapeshellarg($modulePath);
            exec($pintCommand, $output, $returnCode);

            if ($isUsingExistingModel) {
                $pintCommand = base_path('vendor/bin/pint').' --parallel '.escapeshellarg($existingModel['path']);
                exec($pintCommand, $output, $returnCode);
            }

            if ($returnCode === 0) {
                $this->info('✅ Code style fixed');
            } else {
                $this->warn('⚠️ Code style fix completed with warnings');
            }
        }

        // Show summary
        $this->showGenerationSummary($modelAction, $existingModel, $moduleName);
    }

    protected function toArrayElement($array)
    {
        $str = '';
        foreach ($array as $val) {
            $str .= "'$val'".',';
        }

        return substr($str, 0, -1);
    }

    protected function getRouteUrlPrefix($routePrefix, $module)
    {
        if ($routePrefix) {
            return $routePrefix.$module;
        }

        return $module;
    }

    protected function getStubDir($template)
    {
        $templateDir = config('laravolt.thunderclap.templates.'.$template);

        $dirApp = $templateDir;
        $dirVendor = __DIR__.'/../../stubs/'.$templateDir;

        // First, we are looking for user defined path
        if (is_dir($dirApp)) {
            return $dirApp;
        }

        // If not exists, fallback to default template from vendor
        if (is_dir($dirVendor)) {
            return $dirVendor;
        }

        // Throw exception if both directory doesn't exists
        throw new \InvalidArgumentException(sprintf('Invalid directory for template named "%s"', $template));
    }

    /**
     * Enhance existing model with required traits and searchable columns
     */
    protected function enhanceExistingModel(array $modelInfo, $columns): void
    {
        $this->info("Enhancing existing model: {$modelInfo['class']}");

        // Check what enhancements are needed
        $enhancement = $this->modelDetector->needsEnhancement($modelInfo['class']);

        if (! $enhancement['needs_enhancement'] && $enhancement['has_searchable_columns']) {
            $this->info('✓ Model already has all required traits and searchable columns');

            return;
        }

        // Create backup
        $backup = $this->modelEnhancer->createBackup($modelInfo['path']);
        $this->info("✓ Created backup: $backup");

        try {
            // Get searchable columns from transformer
            $searchableColumns = [];
            if ($this->transformer) {
                $searchableString = $this->transformer->toSearchableColumns();
                $searchableColumns = array_map('trim', explode(',', trim($searchableString, "'")));
                $searchableColumns = array_filter($searchableColumns, function ($col) {
                    return ! empty($col) && $col !== "''";
                });
            }

            // Enhance the model
            $success = $this->modelEnhancer->enhanceModel($modelInfo, $enhancement, $searchableColumns);

            if ($success) {
                $this->info('✓ Successfully enhanced existing model');

                if (! empty($enhancement['missing_traits'])) {
                    $this->info('  - Added traits: '.implode(', ', array_map('class_basename', $enhancement['missing_traits'])));
                }

                if (! $enhancement['has_searchable_columns'] && ! empty($searchableColumns)) {
                    $this->info('  - Added searchableColumns property');
                }

                $this->modelEnhancer->removeBackup($backup);
                $this->info("✓ Backup removed: $backup");
            } else {
                $this->error('✗ Failed to enhance model');
            }
        } catch (\Exception $e) {
            $this->error("✗ Error enhancing model: {$e->getMessage()}");
            $this->info('Restoring from backup...');
            $this->modelEnhancer->restoreFromBackup($modelInfo['path'], $backup);
        }
    }

    /**
     * Show generation summary
     */
    protected function showGenerationSummary(string $modelAction, ?array $existingModel, string $moduleName): void
    {
        $this->newLine();
        $this->info('🎉 Module generation completed!');
        $this->newLine();

        $this->line('<fg=cyan>Summary:</fg=cyan>');
        $this->line("  Module: <fg=yellow>{$moduleName}</fg=yellow>");

        switch ($modelAction) {
            case 'enhance':
                $this->line("  Model: <fg=green>Enhanced existing {$existingModel['class']}</fg=green>");
                break;
            case 'skip':
                $this->line("  Model: <fg=yellow>Skipped (using existing {$existingModel['class']})</fg=yellow>");
                break;
            default:
                $this->line('  Model: <fg=green>Created new model in module</fg=green>');
        }

        $this->newLine();
        $this->line('<fg=cyan>Next steps:</fg=cyan>');
        $this->line('  1. Review the generated code');
        $this->line('  2. Update routes and controllers as needed');
        $this->line('  3. Run migrations if not already done');

        if ($modelAction === 'enhance') {
            $this->line('  4. Test the enhanced model functionality');
        }

        $this->newLine();
    }

    /**
     * Post-process controller to use existing model imports
     */
    protected function postProcessControllerForExistingModel(string $controllerPath, array $existingModel, string $moduleName): void
    {
        if (! File::exists($controllerPath)) {
            return;
        }

        $content = File::get($controllerPath);

        // Replace the model import with existing model
        $moduleModelImport = "use Modules\\{$moduleName}\\Models\\{$moduleName};";
        $existingModelImport = "use {$existingModel['class']};";

        $content = str_replace($moduleModelImport, $existingModelImport, $content);

        File::put($controllerPath, $content);

        $this->info("✓ Updated controller to use existing model: {$existingModel['class']}");
    }
}
