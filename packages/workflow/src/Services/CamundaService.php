<?php

namespace Laravolt\Camunda\Services;

use Laravolt\Camunda\CamundaForm;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravolt\Camunda\FieldFormatter\CamundaFormatter;
use Laravolt\Camunda\Models\Form;

class CamundaService
{
    private function changeCamundaDateFormat($data)
    {
        $variables = $data['variables'];
        foreach ($variables as $key => $datum) {
            if (isset($datum['jenis'])) {
                if ($datum['jenis'] === 'date') {
                    $variables[$key]['value'] = Carbon::parse($datum['value'])->toAtomString();
                }
            }
        }

        $data['variables'] = $variables;

        return $data;
    }

    private function clientGet($url, $params = [])
    {
        $client = new Client();
        //try {
        $response = $client->request('GET', config('laravolt.camunda.api.url') . $url, [
            'query' => $params,
            'verify' => false
        ]);
        //} catch (GuzzleException $e) {
        //    \Log::error($e, ['camunda-service']);
        //    throw $e;
        //}

        return json_decode($response->getBody());
    }

    private function clientPost($url, $data)
    {
        $data = $this->changeCamundaDateFormat($data);
        $client = new Client();

        //try {
        $response = $client->request('POST', config('app.camunda_host') . $url, ['json' => $data]);
        //} catch (GuzzleException $e) {
        //    \Log::error($e, ['camunda-service']);
        //    throw $e;
        //}

        return json_decode($response->getBody());
    }

    private function clientPut($url, $data)
    {
        $data = $this->changeCamundaDateFormat($data);
        $client = new Client();

        //try {
        $response = $client->request('PUT', config('app.camunda_host') . $url, ['json' => $data]);
        //} catch (GuzzleException $e) {
        //    \Log::error($e, ['camunda-service']);
        //    throw $e;
        //}

        return json_decode($response->getBody());
    }

    public function getProcessDefinitionId($processDefinitionKey)
    {
        return $this->clientGet("/process-definition/key/$processDefinitionKey");
    }

    public function getListProcessInstance($processDefinitionKey, $firstResult = 0)
    {
        $processDefinitionId = $this->getProcessDefinitionId($processDefinitionKey)->id;

        return $this->clientGet('/process-instance', [
            'processDefinitionId' => $processDefinitionId,
            'maxResult' => 10,
            'firstResult' => $firstResult,
        ]);
    }

    public function getProcessInstance($processInstanceId)
    {
        return $this->clientGet("/process-instance/$processInstanceId");
    }

    public function getProcessDefintionXML($processDefinitionKey)
    {
        return $this->clientGet("/process-definition/key/$processDefinitionKey/xml");
    }

    public function getListAllVariable($processDefinitionKey)
    {
        $listAllVariable = [];
        $processInstanceLists = $this->getProcessInstance($processDefinitionKey);

        foreach ($processInstanceLists as $processInstanceList) {
            $listAllVariable[] = $this->clientGet("/process-instance/{$processInstanceList->id}/variables");
        }

        return $listAllVariable;
    }

    public function getTaskOfProcess($processInstanceId)
    {
        return $this->clientGet('/task', ['processInstanceId' => $processInstanceId]);
    }

    public function getTask($taskId)
    {
        return $this->clientGet("/task/{$taskId}");
    }

    public function getTaskInActivity($processDefinitionKey, $tasks)
    {
        $task = implode(',', $tasks);

        return $this->clientGet('/task', ['processDefinitionKey' => $processDefinitionKey, 'taskDefinitionKeyIn' => $task]);
    }

    /*
     * Untuk mengambil variabel dari sebuah task
     * */
    public function getTaskVariables($processDefinitionKey)
    {
        //$tasks      = $this->getTaskOfProcess($processDefinitionKey);
        //$resultTask = [];
        //foreach ($tasks as $task) {
        //    $variable              = $this->clientGet("/task/{$task->id}/variables");
        //    $resultTask[$task->id] = [
        //        'name'     => $task->name,
        //        'variable' => $variable,
        //    ];
        //}
        //
        //dd($resultTask);
    }

    public function getFormVariablesFromDatabase($processDefinitionKey, $taskName, $formName)
    {
        return CamundaForm::where('process_definition_key', $processDefinitionKey)->where(
            'task_name',
            $taskName
        )->where('form_name', $formName)->orderBy('field_order')->get();
    }

    public function deleteProcessInstance($processInstanceId)
    {
        $client = new Client();
        //try {
        $response = $client->request('DELETE', config('app.camunda_host') . '/process-instance/' . $processInstanceId);
        //} catch (GuzzleException $e) {
        //    \Log::error($e, ['camunda-service']);
        //    throw $e;
        //}
        //TODO if reponse status 204 then delete data in database (call function to delete it)

        return json_decode($response->getBody());
    }

    public function submitStartForm(string $processDefinitionKey, Request $request)
    {
        //TODO ubah typehint dari Request ke array biasa, mengurangi ketergantungan dengan Controller (HTTP Layer)
        $taskName = $request->task_name;
        $formName = $request->form_name;

        $fields = Form::getFields($processDefinitionKey, $taskName);
        $camundaFields = CamundaFormatter::format($request->all(), $fields);

        $data = [
            'variables' => $camundaFields,
        ];

        return $this->clientPost("/process-definition/key/$processDefinitionKey/submit-form", $data);
    }

    public function submitTaskForm(string $taskId, string $processDefinitionKey, Request $request)
    {
        $taskName = $request->task_name;
        $formName = $request->form_name;
        $formVariables = $this->getFormVariablesFromDatabase($processDefinitionKey, $taskName, $formName);

        $data = [];
        foreach ($formVariables as $formVariable) {
            $data[$formVariable->field_name] = [
                'value' => $request->{$formVariable->field_name},
                'jenis' => $formVariable->field_type,
            ];
        }

        if (! $data) {
            $data[$taskName]['value'] = 'ok';
        }

        $data = [
            'variables' => $data,
        ];

        #  dd($data);

        return $this->clientPost("/task/$taskId/submit-form", $data);
    }

    public function updateTask(\stdClass $task, array $data)
    {
        $mapping = DB::table('camunda_task')->where('task_id', $task->id)->first();

        $formVariables = $this->getFormVariablesFromDatabase(
            $mapping->process_definition_key,
            $mapping->task_name,
            $mapping->form_type
        );

        $data = [];
        foreach ($formVariables as $formVariable) {
            $data[$formVariable->field_name] = [
                'value' => Arr::get($data, $formVariable->field_name),
                'jenis' => $formVariable->field_type,
            ];
        }

        if (! $data) {
            $data[$mapping->task_name]['value'] = 'ok';
        }

        $data = [
            'variables' => $data,

            // keep old value, entah kenapa kalau ga dikasih value dibawah, nilainya jadi kereset ketika update
            'name' => $task->name,
            'priority' => $task->priority,
        ];

        return $this->clientPut("/task/{$task->id}", $data);
    }

    public function saveData(
        string $processDefinitionKey,
        string $taskName,
        string $formName,
        Request $data,
        string $processInstanceId,
        string $taskId
    ) {
        if (! \Schema::hasTable($formName)) {
            return false;
        }

        $fields = CamundaForm::where('process_definition_key', $processDefinitionKey)->where(
            'task_name',
            $taskName
        )->where('form_name', $formName)->get();

        $dataInsert = [];
        foreach ($fields as $field) {
            $dataInsert[$field->field_name] = $data->{$field->field_name};
        }

        $dataInsert['process_instance_id'] = $processInstanceId;
        $dataInsert['task_id'] = $taskId;
        $dataInsert['created_by'] = \Auth::user()->id;
        $dataInsert['created_at'] = now();

        return DB::table($formName)->insert($dataInsert);
    }

    public function getAllInstance($processDefinitionKey)
    {
        return $this->clientGet(
            '/process-instance',
            [
                'processDefinitionKey' => $processDefinitionKey,
            ]
        );
    }

    public function getTaskByTaskDefinitionKey($tasks, $definitionKey)
    {
        return $this->clientGet(
            '/task',
            [
                'taskDefinitionKeyIn' => $tasks,
                'processDefinitionKey' => $definitionKey,
            ]
        );
    }

    //TODO add function to delete data from database if the processInstance is deleted
}
