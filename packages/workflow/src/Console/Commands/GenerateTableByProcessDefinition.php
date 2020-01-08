<?php

namespace Laravolt\Camunda\Console\Commands;

use Illuminate\Console\Command;
use Laravolt\Camunda\CamundaForm;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GenerateTableByProcessDefinition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'camunda:generateTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk melakukan generate tabel berdasarkan process definition';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $camundaForms = CamundaForm::all();
        $tableGenerateds = [];
        foreach ($camundaForms as $camundaForm) {
            //prefix t
            $prefixName = 't_'.$camundaForm->form_name;
            $tableGenerateds[$prefixName][] = [
                'name' => $camundaForm->field_name,
                'type' => $camundaForm->field_type,
            ];
        }

        foreach ($tableGenerateds as $tableName => $tableColumns) {
            $tableColumns = collect($tableColumns)->unique(function ($item) {
                return $item['name'];
            });

            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($tableColumns) {
                    $table->bigIncrements('id');
                    $table->string('process_instance_id')->nullable();
                    $table->string('task_id')->nullable();
                    $this->columns($table, $tableColumns);

                    $table->bigInteger('created_by')->nullable();
                    $table->bigInteger('updated_by')->nullable();
                    $table->timestamps();
                });
                $this->info($tableName . ' was created');
            } else {
                $this->info($tableName . ' is exists, should check new columns');
                Schema::table($tableName, function (Blueprint $table) use ($tableColumns) {
                    $this->columns($table, $tableColumns);
                });
                $this->info($tableName . ' was created');
            }

            if (! Schema::hasColumn($tableName, 'no_agenda')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('no_agenda')->nullable();
                });
            }
        }
    }

    protected function columns(Blueprint $table, $columns)
    {
        foreach ($columns as $tableColumn) {
            $fieldName = $tableColumn['name'];

            if (Schema::hasColumn($table->getTable(), $fieldName)) {
                $this->warn(sprintf('Kolom %s.%s sudah ada, skip.', $table->getTable(), $fieldName));

                continue;
            }

            switch ($tableColumn['type']) {
                case 'booelan':
                    $table->boolean($fieldName)->nullable();

                    break;
                case 'integer':
                    $table->integer($fieldName)->nullable();

                    break;
                case 'date':
                    $table->date($fieldName)->nullable();

                    break;
                case 'wysiwyg':
                case 'text':
                    $table->text($fieldName)->nullable();

                    break;
                case 'image':
                case 'file':
                case 'dropdownDB':
                case 'dropdown':
                case 'string':
                default:
                    $table->string($fieldName)->nullable();

                    break;
            }
        }
    }
}
