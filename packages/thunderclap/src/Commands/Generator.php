<?php
namespace Laravolt\Thunderclap\Commands;

use Illuminate\Console\Command;
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
    protected $signature = "laravolt:clap {--table= : table you want to generate the code skeleton}";

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

        $namespace = config('thunderclap.namespace');
        $moduleName = Str::singular(str_replace('_', '', title_case($table)));
        $containerPath = config('thunderclap.target_dir', base_path('modules'));
        $modulePath = $containerPath . DIRECTORY_SEPARATOR . $moduleName;

        // 1. check existing module
        if (is_dir($modulePath)) {
            $overwrite = $this->confirm("Folder {$modulePath} already exist, do you want to overwrite it?");
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
        $stubs = __DIR__ . '/../../stubs';
        $this->info('Copying module skeleton into ' . $modulePath);
        File::copyDirectory($stubs, $modulePath);

        $templates = [
            'module-name'  => str_replace('_', '-', Str::singular($table)),
            'route-prefix' => config('thunderclap.routes.prefix'),
        ];

        // 4. rename file and replace common string
        $search = [
            ':Namespace:',
            ':module_name:',
            ':module-name:',
            ':module name:',
            ':Module Name:',
            ':moduleName:',
            ':ModuleName:',
            ':SEARCHABLE_COLUMNS:',
            ':VALIDATION_RULES:',
            ':LANG_FIELDS:',
            ':TABLE_HEADERS:',
            ':TABLE_FIELDS:',
            ':DETAIL_FIELDS:',
            ':FORM_CREATE_FIELDS:',
            ':FORM_EDIT_FIELDS:',
            ':TABLE_VIEW_FIELDS:',
            ':VIEW_EXTENDS:',
            ':route-prefix:',
            ':route-middleware:',
            ':route-url-prefix:',
        ];
        $replace = [
            $namespace,
            snake_case(Str::singular($table)),
            $templates['module-name'],
            str_replace('_', ' ', strtolower(Str::singular($table))),
            ucwords(str_replace('_', ' ', Str::singular($table))),
            lcfirst($moduleName),
            $moduleName,
            $this->transformer->toSearchableColumns(),
            $this->transformer->toValidationRules(),
            $this->transformer->toLangFields(),
            $this->transformer->toTableHeaders(),
            $this->transformer->toTableFields(),
            $this->transformer->toDetailFields(lcfirst($moduleName)),
            $this->transformer->toFormCreateFields(),
            $this->transformer->toFormEditFields(),
            $this->transformer->toTableViewFields(),
            config('thunderclap.view.extends'),
            $templates['route-prefix'],
            $this->toArrayElement(config('thunderclap.routes.middleware')),
            $this->getRouteUrlPrefix($templates['route-prefix'], $templates['module-name']),
        ];

        foreach (File::allFiles($modulePath) as $file) {
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
                    $newFile = Str::replaceLast('Controller', $moduleName."Controller", $newFile);
                }

                $this->info($newFile);
                $this->packerHelper->replaceAndSave($file, $search, $replace, $newFile, $deleteOriginal);

            }
        }
    }

    protected function toArrayElement($array)
    {
        $str = "";
        foreach ($array as $val) {
            $str .= "'$val'" . ",";
        }

        return substr($str, 0, -1);
    }

    protected function getRouteUrlPrefix($routePrefix, $module)
    {
        if ($routePrefix) {
            return $routePrefix . '.' . $module;
        }

        return $module;
    }
}
