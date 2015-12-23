<?php
namespace Laravolt\Thunderclap\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravolt\Packer\PackerHelper;
use Laravolt\Thunderclap\DBHelper;

class Generator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "clap";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic CRUD';

    /**
     * @var Helper
     */
    protected $DBHelper;

    /**
     * @var PackerHelper
     */
    protected $packerHelper;

    /**
     * Generator constructor.
     */
    public function __construct(DBHelper $DBHelper, PackerHelper $packerHelper)
    {
        parent::__construct();
        $this->DBHelper = $DBHelper;
        $this->packerHelper = $packerHelper;
    }


    public function handle(PackerHelper $helper)
    {

        $tables = $this->DBHelper->listTables();
        $table = $this->choice('Choose table:', $tables, null);

        $columns = collect($this->DBHelper->listColumns($table));
        $columns = $columns->except(config('thunderclap.columns.except'));

        $moduleName = str_replace('_', '', title_case($table));
        $containerPath = base_path('modules');
        $modulePath = $containerPath . DIRECTORY_SEPARATOR . $moduleName;

        // 1. check existing module
        if (is_dir($modulePath)) {
            $overwrite = $this->confirm("Module {$moduleName} already exist, do you want to overwrite it?");
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

        // 4. rename file and replace common string
        $search = [':module_name:', ':module-name:', ':module name:', ':Module Name:', ':moduleName:', ':ModuleName:'];
        $replace = [
            snake_case($table),
            str_replace('_', '-', $table),
            str_replace('_', ' ', strtolower($table)),
            ucwords(str_replace('_', ' ', $table)),
            str_replace('_', '', camel_case($table)),
            str_replace('_', '', title_case($table))
        ];

        foreach (File::allFiles($modulePath) as $file) {
            if (is_file($file)) {

                $newFile = false;

                if (Str::endsWith($file, '.stub')) {
                    $newFile = Str::substr($file, 0, -5);
                }

                $this->packerHelper->replaceAndSave($file, $search, $replace, $newFile);

                if ($newFile) {
                    File::delete($file);
                }
            }
        }

        // 4. inject generated code skeleton
    }

}
