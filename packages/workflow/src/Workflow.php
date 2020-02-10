<?php

declare(strict_types=1);

namespace Laravolt\Workflow;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Camunda\Models\ProcessInstance;
use Laravolt\Camunda\Models\ProcessInstanceHistory;
use Laravolt\Camunda\Models\Task;
use Laravolt\Camunda\Models\TaskHistory;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Entities\Multirow;
use Laravolt\Workflow\Entities\Payload;
use Laravolt\Workflow\Enum\FormType;
use Laravolt\Workflow\Enum\ProcessStatus;
use Laravolt\Workflow\Enum\TaskStatus;
use Laravolt\Workflow\Events\ProcessCompleted;
use Laravolt\Workflow\Events\ProcessStarted;
use Laravolt\Workflow\Events\TaskCompleted;
use Laravolt\Workflow\Events\TaskDrafted;
use Laravolt\Workflow\Events\TaskUpdated;
use Laravolt\Workflow\FieldFormatter\CamundaFormatter;
use Laravolt\Workflow\FieldFormatter\DbFormatter;
use Laravolt\Workflow\Models\AutoSave;
use Laravolt\Workflow\Models\CamundaForm;
use Laravolt\Workflow\Models\Form;
use Laravolt\Workflow\Presenters\StartForm;
use Laravolt\Workflow\Presenters\TaskEditForm;
use Laravolt\Workflow\Traits\DataRetrieval;

class Workflow implements Contracts\Workflow
{
    use DataRetrieval;

    /**
     * Worflow constructor.
     */
    public function __construct()
    {
    }

    public function createStartForm(Module $module): StartForm
    {
        $processDefinition = ProcessDefinition::byKey($module->processDefinitionKey)->fetch();
        $module->startTaskName = $this->validateTaskName($module->startTaskName, $processDefinition);

        return StartForm::make($module, $processDefinition);
    }

    public function editStartForm(Module $module, string $processInstanceId)
    {
        $processInstance = (new ProcessInstance($processInstanceId))->fetch();

        $mapping = DB::table('camunda_task')
            ->where('process_instance_id', $processInstance->id)
            ->whereNull('task_id')
            ->first();

        if (!$mapping) {
            throw (new ModelNotFoundException())->setModel($processInstance);
        }

        return StartForm::makeFromInstance($module, $processInstance);
    }

    public function startProcess(Module $module, array $data): ProcessInstance
    {
        // Pastikan Process Definition Key valid dengan memanggil API ke Camunda REST.
        $processDefinition = ProcessDefinition::byKey($module->processDefinitionKey)->fetch();

        // Validasi Start Task Name di definisi modul dengan start task name hasil pembacaan BPMN.
        // TODO: proses ini deprecated, karena config start_task_name di modul sudah bisa dihilangkan
        // dengan asumsi start_task_name yang valid adalah sesuai BPMN. Tidak ada custom start_task_name.
        $module->startTaskName = $this->validateTaskName($module->startTaskName, $processDefinition);

        // Wrap $data hasil inputan user untuk dilakukan proses sanitize, cleansing, dan validating.
        $payload = Payload::make($module, $module->startTaskName, $data);

        $processInstance = DB::transaction(function () use ($processDefinition, $module, $data, $payload) {
            // Memulai proses, dengan membuat Process Instance baru di Camunda.
            $processInstance = $processDefinition->startInstance(
                $payload->toCamundaVariables(),
                $payload->getBusinessKey()
            );

            $additionalData = [
                'process_instance_id' => $processInstance->id,
            ];

            $mainFormId = null;
            $mainFormName = null;

            // Data inputan sebuah task bisa disimpan ke satu atau lebih form (tabel).
            // Syaratnya, hanya ada satu MAIN_FORM, dan sisanya adalah SUB_FORM.
            // Flagnya ada di kolom camunda_form.type
            foreach ($payload->toFormFields() as $form => $fields) {
                $dbFields = $fields['fields'];

                if ($fields['type'] == FormType::MAIN_FORM) {
                    $data = $dbFields + $additionalData;
                    $formId = $this->insertData($form, $data);
                    $mainFormId = $formId;
                    $mainFormName = $form;
                } else {
                    $subForm = [
                        'parent_id' => $mainFormId,
                        'parent_form' => $mainFormName,
                    ];
                    $data = $dbFields + $additionalData + $subForm;
                    $formId = $this->insertData($form, $data);
                }

                DB::table('camunda_task')->insert([
                    'process_definition_key' => $processDefinition->key,
                    'process_instance_id' => $processInstance->id,
                    'task_id' => null,
                    'form_type' => $form,
                    'form_id' => $formId,
                    'task_name' => $module->startTaskName,
                    'created_at' => now(),
                    'status' => ProcessStatus::ACTIVE,
                    'business_key' => $payload->getBusinessKey(),
                    'traceable' => json_encode(collect($payload->data)->only(config('laravolt.workflow.traceable')) ?? []),
                ]);
            }

            $this->prepareNextTask($processInstance);

            return $processInstance;
        });

        event(new ProcessStarted($processInstance, $payload, auth()->user()));

        return $processInstance;
    }

    public function saveSubProcess(ProcessInstance $processInstance, $data): ProcessInstance
    {
        return DB::transaction(function () use ($processInstance, $data) {
            $additionalData = [
                'process_instance_id' => $processInstance->id,
            ];
            $data = $data + $additionalData;

            $form = $processInstance->processDefinition()->getStartTaskName();
            $formId = $this->insertData($form, $data);
            DB::table('camunda_task')->insert([
                'process_definition_key' => $processInstance->processDefinition()->key,
                'process_instance_id' => $processInstance->id,
                'task_id' => null,
                'task_name' => $form,
                'form_type' => $form,
                'form_id' => $formId,
                'created_at' => now(),
                'status' => ProcessStatus::ACTIVE,
                'business_key' => $processInstance->businessKey ?? null,
            ]);

            $this->prepareNextTask($processInstance);

            $payload = new Payload(['data' => $data]);
            event(new ProcessStarted($processInstance, $payload, auth()->user()));

            return $processInstance;
        });
    }

    /**
     * @deprecated
     */
    public function updateProcess(string $processInstanceId, array $data): ProcessInstance
    {
        $processInstance = (new ProcessInstance($processInstanceId))->fetch();

        $mapping = DB::table('camunda_task')
            ->where('process_instance_id', $processInstance->id)
            ->whereNull('task_id')
            ->first();

        $fields = Form::getFields($mapping->process_definition_key, $mapping->task_name);
        $camundaFields = CamundaFormatter::format($data, $fields);
        $dbFields = DbFormatter::format($data, $fields);

        $table = $mapping->form_type;

        if (!$table) {
            throw new \DomainException(sprintf('Tabel %s tidak ditemukan', $table));
        }

        $processInstance->setVariables($camundaFields);

        DB::transaction(function () use ($processInstance, $dbFields, $table, $mapping) {
            $additionalData = [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ];

            DB::table($table)->where('id', $mapping->form_id)->update($dbFields + $additionalData);

            DB::table('camunda_task')
                ->where('process_instance_id', $processInstance->id)
                ->whereNull('task_id')
                ->update([
                    'created_at' => now(),
                ]);
        });

        return $processInstance;
    }

    public function undoProcess(string $processInstanceId): ProcessInstance
    {
        $processInstance = (new ProcessInstance($processInstanceId))->fetch();

        DB::transaction(function () use ($processInstance) {
            [$previousActivity, $currentActivity] = $processInstance->undo();

            // Delete current active task
            DB::table('camunda_task')
                ->where('task_id', $currentActivity->taskId)
                ->delete();

            // Update last completed task
            DB::table('camunda_task')
                ->where('task_id', $previousActivity->taskId)
                ->update(['status' => TaskStatus::NEW, 'form_id' => null]);
        });

        return $processInstance;
    }

    public function moveProcessTo(string $processInstanceId, string $taskDefinitionKey): ProcessInstance
    {
        $processInstance = (new ProcessInstance($processInstanceId))->fetch();

        DB::transaction(function () use ($processInstance, $taskDefinitionKey) {
            $activities = $processInstance->moveTo($taskDefinitionKey);
            $ids = $activities->pluck('taskId')->toArray();

            DB::table('camunda_task')->whereIn('task_id', $ids)->update(['status' => TaskStatus::CANCELED]);

            $this->prepareNextTask($processInstance);
        });

        return $processInstance;
    }

    public function deleteProcess(string $processInstanceId)
    {
        $processInstance = (new ProcessInstance($processInstanceId))->fetch();

        $mapping = DB::table('camunda_task')->where('process_instance_id', $processInstance->id)->get();

        if ($mapping->isEmpty()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Process instance %s tidak tercatat dalam tabel mapping `camunda_task`, '.
                    'silakan hapus secara manual.',
                    $processInstance->id
                )
            );
        }

        DB::transaction(function () use ($processInstance, $mapping) {
            // Delete camunda process instance
            try {
                $processInstance->deleteProcessInstance();
            } catch (ClientException $e) {
                \Log::debug(sprintf('Mencoba menghapus non-exists process instance dengan id %s',
                    $processInstance->id));
            } finally {
                // Delete local data for each form
                foreach ($mapping as $taskMapping) {
                    DB::table($taskMapping->form_type)->delete($taskMapping->form_id);
                }

                // Delete mapping data
                DB::table('camunda_task')->where('process_instance_id', $processInstance->id)->delete();
            }
        });
    }

    public function submitTask(Module $module, string $taskId, array $data, bool $isDraft = false)
    {
        //coba damar
        foreach ($data as $keys => $lists) {
            if (is_array($lists)) {
                foreach ($lists as $key => $list) {
                    foreach ($list as $k => $v) {
                        if (is_array($v)) {
                            $convert = json_encode($v);
                            $data[$keys][$key][$k] = $convert;
                        }
                    }
                }
            }
        }

        $task = (new Task($taskId))->fetch();
        $processInstance = $task->processInstance();

        DB::transaction(function () use (
            $module,
            $task,
            $processInstance,
            $data,
            $isDraft
        ) {
            $payload = Payload::make($module, $task->taskDefinitionKey, $data);

            foreach ($payload->toFormFields() as $table => $fields) {
                $dbFields = $fields['fields'];

                $existing = DB::table($table)
                    ->where('process_instance_id', $processInstance->id)
                    ->where('task_id', $task->id)
                    ->first();

                $additionalData = [
                    'process_instance_id' => $processInstance->id,
                    'task_id' => $task->id,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ];

                $data = $dbFields + $additionalData;
                $formId = null;

                if (!$existing) {
                    $formId = $this->insertData($table, $data);
                } else {
                    $formId = $this->updateData($existing->id, $table, $data);
                }

                DB::table('camunda_task')
                    ->where('process_instance_id', $processInstance->id)
                    ->where('task_id', $task->id)
                    ->update(
                        [
                            'form_id' => $formId,
                            'status' => $isDraft ? TaskStatus::DRAFT : TaskStatus::COMPLETED,
                            'updated_at' => now(),
                        ]
                    );

                if ($isDraft) {
                    event(new TaskDrafted($task, $payload, auth()->user()));
                } else {
                    $task->submit($payload->toCamundaVariables());

                    $this->prepareNextTask($processInstance);

                    event(new TaskCompleted($task, $payload, auth()->user()));

                    $subProcesses = $task->processInstance()->getSubProcess();
                    if (!empty($subProcesses)) {
                        foreach ($subProcesses as $process) {
                            $this->saveSubProcess($process, $this->getDataByProcessInstanceId($processInstance->id));
                        }
                    }

                    // cek apakah process sudah selesai
                    $processInstanceHistory = (new ProcessInstanceHistory($processInstance->id))->fetch();

                    $status = $processInstanceHistory->state ?? null;
                    if ($status == ProcessStatus::COMPLETED) {
                        // 1. update process status di camunda_task
                        DB::table('camunda_task')
                            ->whereNull('task_id')
                            ->where('process_instance_id', $processInstance->id)
                            ->update(['status' => $status]);

                        // 2. trigger event ProcessCompleted
                        event(new ProcessCompleted($processInstance, auth()->user()));
                    }
                }

                AutoSave::query()->where('task_id', $task->id)->where('user_id', auth()->id())->delete();

                // TODO: deprecated
                event('workflow.task.saved',
                    [$module, $processInstance, $task->taskDefinitionKey, $table, $data, $formId]);
            }
        });

        return $task;
    }

    public function updateTask(Module $module, string $taskId, array $data)
    {
        $mapping = DB::table('camunda_task')->where('task_id', $taskId)->first();

        $taskName = Arr::get($data, '_task_name', $mapping->task_name);
        $table = Arr::get($data, '_form_name', $mapping->form_type);

        if (!$table) {
            throw new \DomainException(sprintf('Tabel %s tidak ditemukan', $table));
        }

        $payload = Payload::make($module, $taskName, $data);

        DB::transaction(function () use ($module, $taskId, $payload) {
            $now = now();
            $additionalData = [
                'updated_by' => auth()->id(),
                'updated_at' => $now,
            ];

            foreach ($payload->toFormFields() as $table => $fields) {
                $dbFields = $fields['fields'];

                $existing = DB::table($table)->where('task_id', $taskId)->first();
                if (!$existing) {
                    continue;
                }

                $this->updateData($existing->id, $table, $dbFields + $additionalData);

                DB::table('camunda_task')->where('task_id', $taskId)->update([
                    'updated_at' => $now,
                ]);
            }
        });

        $taskHistory = null;

        try {
            $taskHistory = (new TaskHistory($taskId))->fetch();
        } catch (ClientException $e) {
            app('sentry')->captureException($e);
        } finally {
            event(new TaskUpdated($taskHistory, $payload, auth()->user()));

            return $mapping;
        }
    }

    public function completedTasks($processInstanceId, array $whitelist = []): array
    {
        $query = DB::table('camunda_task')
            ->orderBy('created_at')
            ->where('process_instance_id', $processInstanceId)
            ->where(function (Builder $query) {
                $query->whereNull('task_id')->orWhere(function (Builder $query2) {
                    $query2->where('status', TaskStatus::COMPLETED)
                        ->whereNotNull('task_id');
                });
            });

        if (!empty($whitelist)) {
            $query->whereIn('task_name', $whitelist);
        }

        $tasks = $query->get()->toArray();

        return $tasks;
    }

    public function editTaskForm(Module $module, string $taskId)
    {
        $mapping = DB::table('camunda_task')
            ->where('task_id', $taskId)
            ->first();

        if (!$mapping) {
            throw new ModelNotFoundException();
        }

        return TaskEditForm::make($module, $mapping);
    }

    protected function validateTaskName(?string $taskName, ProcessDefinition $processDefinition)
    {
        if (!$taskName) {
            $taskName = $processDefinition->getStartTaskName();
        }

        return $taskName;
    }

    protected function getFormField($processDefinitionKey, $task)
    {
        $form_name = CamundaForm::select('form_name')
            ->where('task_name', '=', $task)
            ->where('process_definition_key', '=', $processDefinitionKey)
            ->distinct()
            ->get();
        $form_field = [];
        foreach ($form_name as $form) {
            $form_field[$form->form_name] = DB::table('camunda_form')
                ->select('field_name')
                ->where('task_name', '=', $task)
                ->where('form_name', '=', $form->form_name)
                ->get();
        }

        return $form_field;
    }

    protected function getTable(?string $key, ?string $startTaskName)
    {
        $table = Form::getFormName($key, $startTaskName);
        if (!$table) {
            throw new \DomainException(
                sprintf(
                    'Tabel untuk proses %s->%s tidak ditemukan. Silakan cek kembali mapping di camunda_form.',
                    $key,
                    $startTaskName
                )
            );
        }

        return $table;
    }

    protected function insertData(string $table, array $data)
    {
        $additionalData = [
            'created_by' => auth()->id(),
            'created_at' => now(),
        ];

        [$mainTableData, $hasManyData] = $this->filterAndPartition($data, $table);

        $id = DB::table($table)->insertGetId($mainTableData + $additionalData);

        $this->saveHasMany($hasManyData, $additionalData, $table, $id);

        return $id;
    }

    protected function updateData($id, string $table, array $data)
    {
        $additionalData = [
            'created_by' => auth()->id(),
            'created_at' => now(),
        ];

        [$mainTableData, $hasManyData] = $this->filterAndPartition($data, $table);

        DB::table($table)->where('id', $id)->update($mainTableData);

        $this->saveHasMany($hasManyData, $additionalData, $table, $id);

        return $id;
    }

    protected function saveHasMany(array $hasManyData, array $additionalData, string $table, int $id)
    {
        foreach ($hasManyData as $hasManyTable => $rows) {
            DB::table($hasManyTable)->where('form_type', $table)->where('form_id', $id)->delete();
            $ids = [];
            foreach ($rows as $row) {
                $row = collect($row)->filter();
                if ($row->isNotEmpty()) {
                    $row['form_id'] = $id;
                    $row['form_type'] = $table;
                    $ids[] = DB::table($hasManyTable)->insertGetId($row->toArray() + $additionalData);
                }
            }
            if (Schema::hasColumn($table, $hasManyTable)) {
                DB::table($table)->where('id', $id)->update([$hasManyTable => json_encode([$hasManyTable => $ids])]);
            }
        }
    }

    protected function filterAndPartition(array $data, string $table)
    {
        $mainTableData = $hasManyData = [];

        foreach ($data as $key => $item) {
            if ($item instanceof Multirow) {
                $key = $item->key;
                $formDefition = config("workflow.forms.$key");
                $rules = collect($formDefition)->mapWithKeys(function ($item) {
                    //coba damar
                    $nameTemp = str_replace('[]', '', $item['name']);

                    return [$nameTemp => $item['validations']];
                })->toArray();

                $filteredData = [];
                foreach ($item->data as $row) {
                    $validator = Validator::make($row, $rules);
                    if ($validator->passes()) {
                        $filteredData[] = $row;
                    }
                }
                $hasManyData[$key] = $filteredData;
            } else {
                $mainTableData[$key] = $item;
            }
        }

        if (!config('workflow.strict')) {
            $columnListing = Schema::getColumnListing($table);
            $columnListing = collect($columnListing)->combine($columnListing);
            $mainTableData = collect($mainTableData)->intersectByKeys($columnListing)->toArray();
        }

        //coba damar

        foreach ($hasManyData as $keys => $lists) {
            if (is_array($lists)) {
                foreach ($lists as $key => $list) {
                    foreach ($list as $k => $v) {
                        if (is_array($v)) {
                            $convert = json_encode($v);
                            $hasManyData[$keys][$key][$k] = $convert;
                        }
                    }
                }
            }
        }

        return [$mainTableData, $hasManyData];
    }

    protected function prepareNextTask($processInstance)
    {
        $currentTask = $processInstance->currentTask();

        if ($currentTask) {
            DB::table('camunda_task')->insert([
                'task_id' => $currentTask->id,
                'process_instance_id' => $processInstance->id,
                'form_type' => $currentTask->taskDefinitionKey,
                'form_id' => null,
                'task_name' => $currentTask->taskDefinitionKey,
                'process_definition_key' => $processInstance->processDefinition()->key,
                'status' => TaskStatus::NEW,
                'business_key' => $processInstance->businessKey ?? null,
                'created_at' => now(),
            ]);
        }
    }
}
