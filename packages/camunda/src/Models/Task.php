<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class Task extends CamundaModel
{
    protected $processInstance;

    public function processInstance(): ProcessInstance
    {
        if (!$this->processInstance) {
            $this->processInstance = (new ProcessInstance($this->processInstanceId))->fetch();
        }

        return $this->processInstance;
    }

    public function setProcessInstance(ProcessInstance $processInstance)
    {
        $this->processInstance = $processInstance;
    }

    public function setVariables(array $modifications, array $deletions = [])
    {
        $modifications = $this->formatVariables($modifications);
        $deletions = $this->formatVariables($deletions);

        $this->post('variables', [
            'modifications' => $modifications,
            'deletions' => $deletions,
        ], true);
    }

    public function submit(array $data)
    {
        $payload = ['variables' => $this->formatVariables($data)];

        return $this->post('submit-form', $payload);
    }

    public function complete()
    {
        return $this->post('complete');
    }
}
