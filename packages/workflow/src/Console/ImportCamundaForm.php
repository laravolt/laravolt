<?php

namespace Laravolt\Workflow\Console;

use Laravolt\Workflow\CamundaForm;
use Laravolt\Workflow\Services\CamundaService;
use DB;
use Illuminate\Console\Command;
use SimpleXMLElement;

class ImportCamundaForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'camunda:importCamundaForm {processDefKey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import semua process definition yang ada ke camunda form';

    /**
     * @var CamundaService
     */
    private $camundaService;

    /**
     * @var GetCamundaController
     */
    private $getCamunda;

    /**
     * Create a new command instance.
     *
     * @param CamundaService $camundaService
     * @param GetCamundaController $getCamunda
     */
    public function __construct(CamundaService $camundaService)
    {
        parent::__construct();
        $this->camundaService = $camundaService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];
        $processDefKey = $this->argument('processDefKey');
        $xml = $this->camundaService->getProcessDefintionXML($processDefKey);
        $data['instances'] = $xml->bpmn20Xml;

        $xml = new SimpleXMLElement($data['instances']);
        $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $xml->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');

        $calledActivity = $xml->xpath('//bpmn:callActivity');
        foreach ($calledActivity as $call) {
            if (CamundaForm::where('field_name', '=', 'subprocess' . $call['name'])
                ->where('task_name', '=', $call['id'])
                ->where('process_definition_key', '=', $processDefKey)
                ->count() == 0) {
                CamundaForm::insert([
                    'process_definition_key' => $processDefKey,
                    'task_name' => $call['id'],
                    'form_name' => $call['id'],
                    'field_name' => 'subprocess' . $call['name'],
                    'field_label' => 'subprocess' . $call['name'],
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
        $this->getField($startEvents, $processDefKey);
        $userTasks = $xml->xpath('//bpmn:userTask');
        $this->getField($userTasks, $processDefKey);

        return true;
    }

    public function getField($nodes, $processDefKey)
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
}
