<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Routing\Controller;
use Laravolt\Camunda\Models\ProcessDefinition;
use Laravolt\Camunda\Models\ProcessInstance;
use Laravolt\Camunda\Contracts\Workflow;
use Laravolt\Camunda\Entities\Module;
use Laravolt\Camunda\Requests\BasicRequest;
use mysql_xdevapi\Exception;

class TaskController extends Controller
{
    /**
     * @var Workflow
     */
    protected $workflow;

    /**
     * TaskController constructor.
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function store(Module $module, $taskId, BasicRequest $request)
    {
        try {
            $task = $this->workflow->submitTask($module, $taskId, $request->all(), $request->isDraft());
            $subInstance = $task->processInstance()->getSubProcess();
            if (count($subInstance) > 0) {
                $this->checkSubInstance($task);
            }

            $message = __('camunda::message.task.submitted', ['task_name' => $task->name]);
            if ($request->isDraft()) {
                $message = __('camunda::message.task.drafted', ['task_name' => $task->name]);
            }

            if (request()->wantsJson()) {
                return response()->json(['message' => $message]);
            }

            return redirect()->back()->withSuccess($message);
        } catch (ClientException $e) {
            abort($e->getCode(), $e->getMessage());
        } catch (ServerException $e) {
            throw new \Exception(json_decode((string) $e->getResponse()->getBody())->message);
        }
    }

    public function edit(Module $module, $taskId)
    {
        try {
            $form = $this->workflow->editTaskForm($module, $taskId);

            return view('camunda::task.edit', compact('form', 'module'));
        } catch (ClientException $e) {
            abort($e->getCode(), $e->getMessage());
        } catch (\DomainException $e) {
            return redirect()->route('camunda::process.index', $module->id)->withError($e->getMessage());
        }
    }

    public function update(Module $module, $taskId, BasicRequest $request)
    {
        try {
            $mapping = $this->workflow->updateTask($module, $taskId, $request->all());
            $message = __('camunda::message.task.updated', ['task_name' => $module->getTask($mapping->task_name)['label'] ?? '']);

            return redirect()
                ->route('camunda::process.show', [$module->id, $mapping->process_instance_id])
                ->withSuccess($message);
        } catch (ClientException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    protected function checkSubInstance($task)
    {
        $subInstance = $task->processInstance()->getSubProcess();
        foreach ($subInstance as $sub) {
            $processDefKey = new ProcessDefinition($sub->definitionId);
            $processDefKey = $processDefKey->fetch();
            $id = \DB::table('start_' . $processDefKey->key)
                ->insertGetId([
                    'process_instance_id' => $sub->id,
                ]);
            $this->insertVariable($sub, 'start_' . $processDefKey->key);
            \DB::table('camunda_task')
                ->insert([
                   'process_definition_key' => $processDefKey->key,
                    'process_instance_id' => $sub->id,
                    'task_name' => 'start_' . $processDefKey->key,
                    'task_id' => null,
                    'form_type' => 'start_' . $processDefKey->key,
                    'form_id' => $id,
                    'created_at' => now(),
                ]);
        }
    }

    public function insertVariable(ProcessInstance $instance, $tableName)
    {
        $variables = $instance->getVariables();
        foreach ($variables as $k => $v) {
            try {
                if ($v->type == 'Date') {
                    \DB::table($tableName)
                        ->where('process_instance_id', $instance->id)
                        ->update([
                            $k => Carbon::parse($v->value)->format('Y-m-d'),
                        ]);
                } else {
                    \DB::table($tableName)
                        ->where('process_instance_id', $instance->id)
                        ->update([
                            $k => $v->value,
                        ]);
                }
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
