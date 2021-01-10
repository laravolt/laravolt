<?php

namespace Laravolt\Thunderclap\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravolt\Thunderclap\ColumnsTransformer;
use Laravolt\Thunderclap\DBHelper;
use Laravolt\Thunderclap\FileTransformer;

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
                    {--module= : Custom module name you want}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic CRUD';

    protected $DBHelper;

    protected $packerHelper;

    protected $transformer;

    /**
     * Generator constructor.
     *
     * @return void
     */
    public function __construct(DBHelper $DBHelper, FileTransformer $packerHelper, ColumnsTransformer $transformer)
    {
        parent::__construct();

        $this->DBHelper = $DBHelper;
        $this->packerHelper = $packerHelper;
        $this->transformer = $transformer;
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
            'module-name'  => str_replace('_', '-', Str::singular($table)),
            'route-prefix' => config('laravolt.thunderclap.routes.prefix'),
        ];

        // 4. rename file and replace common string
        $replacer = [
            ':Namespace:' => $namespace,
            ':table:' => $table,
            ':module_name:' => snake_case(Str::singular($table)),
            ':module-name:' => $templates['module-name'],
            ':module name:' => str_replace('_', ' ', strtolower(Str::singular($table))),
            ':Module Name:' => $moduleName,
            ':moduleName:' => lcfirst($moduleName),
            ':ModuleName:' => $moduleName,
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
        ];

        foreach (File::allFiles($modulePath, true) as $file) {
            if (is_file($file)) {
                $newFile = $deleteOriginal = false;

                if (Str::endsWith($file, '.stub')) {
                    $newFile = Str::substr($file, 0, -5);
                    $deleteOriginal = true;
                }

                if (Str::endsWith($newFile, 'Model.php')) {
                    $newFile = Str::replaceLast('Model', $moduleName, $newFile);
                }

                if (Str::endsWith($newFile, 'Controller.php')) {
                    $newFile = Str::replaceLast('Controller', $moduleName.'Controller', $newFile);
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

                if (!$newFile) {
                    continue;
                }

                $this->info($newFile);

                try {
                    $this->packerHelper->replaceAndSave($file, array_keys($replacer), array_values($replacer), $newFile, $deleteOriginal);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
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
            return $routePrefix.'.'.$module;
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
}
