<?php

namespace Laravolt\Workflow\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Workflow\Models\CamundaForm;
use SimpleXMLElement;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'workflow:import {processDefinitionKey}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Import form definition dari file BPMN via REST API';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('processDefinitionKey');
        $processDefinition = ProcessDefinition::byKey($key);

        $xml = new SimpleXMLElement($processDefinition->xml());
        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');
        
        $calledActivity = $xml->xpath('//bpmn:callActivity');
        foreach ($calledActivity as $call) {
            if (CamundaForm::where('field_name', '=', 'subprocess' . $call['name'])
                    ->where('task_name', '=', $call['id'])
                    ->where('process_definition_key', '=', $key)
                    ->count() == 0) {
                CamundaForm::insert([
                    'process_definition_key' => $key,
                    'task_name' => $call['id'],
                    'form_name' => $call['id'],
                    'field_name' => 'subprocess' . $call['name'],
                    'field_label' => 'subprocess' . $call['name'],
                    'field_hint' => null,
                    'field_type' => 'hidden',
                    'field_select_query' => null,
                    'field_order' => 0,
                    'field_meta' => null,
                    'segment_group' => null,
                    'segment_order' => null,
                    'called_element' => $call['calledElement'],
                ]);
            }
        }
        $startEvents = $xml->xpath('//bpmn:startEvent');
        $this->getField($startEvents, $key);
        $userTasks = $xml->xpath('//bpmn:userTask');
        $this->getField($userTasks, $key);

        $this->generateTable($key);

        return true;
    }

    protected function getField($nodes, $processDefKey)
    {
        foreach ($nodes as $node) {
            $this->info('Task ' . json_encode($node));
            $called_element = $node['calledElement'];
            $formFields = $node->xpath('bpmn:extensionElements/camunda:formData/camunda:formField');
            if (count($formFields) == 0) {
                DB::table('camunda_form')->insert([
                    'process_definition_key' => $processDefKey,
                    'task_name' => $node['id'],
                    'form_name' => $node['id'],
                    'field_name' => $node['id'],
                    'field_label' => $node['id'],
                    'field_type' => 'hidden',
                    'field_select_query' => null,
                    'field_order' => 0,
                    'field_meta' => null,
                    'segment_group' => null,
                    'segment_order' => null,
                    'called_element' => null,
                ]);
            } else {
                foreach ($formFields as $formField) {
                    if ($formField['type'] == 'enum') {
                        $this->info('Field Enum' . json_encode($formField));

                        $options = [];
                        $fieldOptions = $formField->xpath('camunda:value');
                        foreach ($fieldOptions as $fieldOption) {
                            foreach ((array) $fieldOption as $k => $v) {
                                array_push($options, [$v['id'] => $v['name'] ?? '']);
                            }
                        }
                        $this->info(CamundaForm::where('field_name', '=', $formField['id'])
                            ->where('task_name', '=', $node['id'])
                            ->where('process_definition_key', '=', $processDefKey)
                            ->count());

                        if (CamundaForm::where('field_name', '=', $formField['id'])
                                ->where('task_name', '=', $node['id'])
                                ->where('process_definition_key', '=', $processDefKey)
                                ->count() == 0) {
                            $this->info('Field Added' . json_encode($formField));

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
                                'called_element' => $called_element,
                            ]);
                        } else {
                            $this->info('Field Exist' . json_encode($formField));
                        }
                    } else {
                        if (CamundaForm::where('field_name', '=', $formField['id'])
                                ->where('task_name', '=', $node['id'])
                                ->where('process_definition_key', '=', $processDefKey)
                                ->count() == 0) {
                            #$this->info('Field Added'.json_encode($formField));

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
                                'called_element' => $called_element,
                            ]);
                        } else {
                            $this->info('Field Exist' . json_encode($formField));
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
        }
    }
}
