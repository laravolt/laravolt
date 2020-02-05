<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravolt\Workflow\Models\Bpmn;
use Laravolt\Workflow\Models\CamundaForm;
use SimpleXMLElement;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:import {key?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import BPMN via REST API to populate transactional table + form definition';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keys = (array) $this->argument('key');

        if (empty($keys)) {
            $keys = Bpmn::pluck('filename');
        }

        foreach ($keys as $key) {
            $this->generateFormField($key);
            $this->generateTable($key);
        }

        return true;
    }

    protected function generateFormField($key)
    {
        $key = Str::endsWith($key, '.bpmn') ? $key : $key.'.bpmn';
        $bpmnFile = resource_path("bpmn/$key");

        if (!file_exists($bpmnFile)) {
            $this->warn(sprintf('File tidak ditemukan: %s', $bpmnFile));

            return false;
        }

        $xml = new SimpleXMLElement($bpmnFile, 0, true);
        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');
        $processDefinitionKey = $xml->xpath('//bpmn:process')[0]['id'] ?? null;

        if ($processDefinitionKey) {
            $startEvents = $xml->xpath('//bpmn:startEvent');
            $this->generateField($startEvents, $processDefinitionKey);
            $userTasks = $xml->xpath('//bpmn:userTask');
            $this->generateField($userTasks, $processDefinitionKey);
        }
    }

    protected function generateField($nodes, $processDefKey)
    {
        foreach ($nodes as $node) {
            $formFields = $node->xpath('bpmn:extensionElements/camunda:formData/camunda:formField');
            if (count($formFields) == 0) {
                DB::table('camunda_form')->updateOrInsert(
                    [
                        'process_definition_key' => $processDefKey,
                        'form_name' => $node['id'],
                        'field_name' => $node['id'],
                    ],
                    [
                        'task_name' => $node['id'],
                        'field_label' => $node['id'],
                        'field_type' => 'hidden',
                        'field_select_query' => null,
                        'field_order' => 0,
                        'field_meta' => null,
                        'segment_group' => null,
                        'segment_order' => null,
                    ]
                );
            } else {
                foreach ($formFields as $formField) {
                    if ($formField['type'] == 'enum') {
                        $options = [];
                        $fieldOptions = $formField->xpath('camunda:value');
                        foreach ($fieldOptions as $fieldOption) {
                            foreach ((array) $fieldOption as $k => $v) {
                                array_push($options, [$v['id'] => $v['name'] ?? '']);
                            }
                        }

                        if (CamundaForm::where('field_name', '=', $formField['id'])
                                ->where('task_name', '=', $node['id'])
                                ->where('process_definition_key', '=', $processDefKey)
                                ->count() == 0) {
                            DB::table('camunda_form')->insert([
                                'process_definition_key' => $processDefKey,
                                'task_name' => $node['id'],
                                'form_name' => $node['id'],
                                'field_name' => $formField['id'],
                                'field_label' => $formField['label'],
                                'field_type' => $formField['type'],
                                'field_select_query' => json_encode($options),
                                'field_order' => 0,
                                'field_meta' => null,
                                'segment_group' => null,
                                'segment_order' => null,
                            ]);
                        } else {
                            $this->info('Field Exist'.json_encode($formField));
                        }
                    } else {
                        if (CamundaForm::where('field_name', '=', $formField['id'])
                                ->where('task_name', '=', $node['id'])
                                ->where('process_definition_key', '=', $processDefKey)
                                ->count() == 0) {
                            DB::table('camunda_form')->insert([
                                'process_definition_key' => $processDefKey,
                                'task_name' => $node['id'],
                                'form_name' => $node['id'],
                                'field_name' => $formField['id'],
                                'field_label' => $formField['label'],
                                'field_type' => $formField['type'],
                                'field_select_query' => null,
                                'field_order' => 0,
                                'field_meta' => null,
                                'segment_group' => null,
                                'segment_order' => null,
                            ]);
                        } else {
                            $this->info('Field Exist'.json_encode($formField));
                        }
                    }
                }
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

    protected function generateTable(string $key)
    {
        $camundaForms = CamundaForm::where('process_definition_key', $key)->get();
        $tableGenerateds = [];
        foreach ($camundaForms as $camundaForm) {
            $prefixName = $camundaForm->form_name;
            $tableGenerateds[$prefixName][] = [
                'name' => $camundaForm->field_name,
                'type' => $camundaForm->field_type,
            ];
        }

        foreach ($tableGenerateds as $tableName => $tableColumns) {
            $tableColumns = collect($tableColumns)->unique(function ($item) {
                return $item['name'];
            });

            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($tableColumns) {
                    $table->bigIncrements('id');
                    $table->string('process_instance_id')->nullable();
                    $table->string('task_id')->nullable();
                    $this->columns($table, $tableColumns);

                    $table->bigInteger('created_by')->nullable();
                    $table->bigInteger('updated_by')->nullable();
                    $table->timestamps();
                });
            } else {
                Schema::table($tableName, function (Blueprint $table) use ($tableColumns) {
                    $this->columns($table, $tableColumns);
                });
            }
        }
    }
}
