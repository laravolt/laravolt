<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class ProcessInstanceHistory extends ProcessInstance
{
    protected function modelUri(): string
    {
        if ($this->key) {
            return 'history/process-instance/key/' . $this->key . $this->tenant();
        } else {
            return 'history/process-instance/' . $this->id;
        }
    }
}
