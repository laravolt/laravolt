<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Contracts;

use Laravolt\Camunda\Models\ProcessInstance;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Presenters\StartForm;

interface Workflow
{
    public function createStartForm(Module $module): StartForm;

    public function editStartForm(Module $module, string $processInstanceId);

    public function startProcess(Module $module, array $data): ProcessInstance;

    public function updateProcess(string $processInstanceId, array $data): ProcessInstance;

    public function deleteProcess(string $processInstanceId);

    public function submitTask(Module $module, string $taskId, array $data, bool $isDraft = false);

    public function updateTask(Module $module, string $taskId, array $data);

    public function completedTasks($processInstanceId, array $whitelist = []): array;
}
