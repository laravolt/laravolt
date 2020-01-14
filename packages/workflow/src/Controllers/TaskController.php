<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Routing\Controller;
use Laravolt\Workflow\Contracts\Workflow;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Requests\BasicRequest;

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

            $message = __('workflow::message.task.submitted', ['task_name' => $task->name]);
            if ($request->isDraft()) {
                $message = __('workflow::message.task.drafted', ['task_name' => $task->name]);
            }

            if (request()->wantsJson()) {
                return response()->json(['message' => $message]);
            }

            return redirect()->back()->withSuccess($message);
        } catch (ClientException $e) {
            report($e);
            abort($e->getCode(), $e->getMessage());
        } catch (ServerException $e) {
            report($e);

            throw new \Exception(json_decode((string) $e->getResponse()->getBody())->message);
        }
    }

    public function edit(Module $module, $taskId)
    {
        try {
            $form = $this->workflow->editTaskForm($module, $taskId);

            return view('workflow::task.edit', compact('form', 'module'));
        } catch (ClientException $e) {
            report($e);
            abort($e->getCode(), $e->getMessage());
        } catch (\DomainException $e) {
            return redirect()->route('workflow::process.index', $module->id)->withError($e->getMessage());
        }
    }

    public function update(Module $module, $taskId, BasicRequest $request)
    {
        try {
            $mapping = $this->workflow->updateTask($module, $taskId, $request->all());
            $message = __(
                'workflow::message.task.updated',
                ['task_name' => $module->getTask($mapping->task_name)['label'] ?? '']
            );

            return redirect()
                ->route('workflow::process.show', [$module->id, $mapping->process_instance_id])
                ->withSuccess($message);
        } catch (ClientException $e) {
            report($e);
            abort($e->getCode(), $e->getMessage());
        }
    }
}
