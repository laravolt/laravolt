<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class ProcessInstanceHistory extends ProcessInstance
{
    public function fetchByBusinessKey($key)
    {
        $url = 'history/process-instance?processInstanceBusinessKey='.$key;

        $processess = $this->get($url);

        return collect($processess);
    }

    protected function modelUri(): string
    {
        if ($this->key) {
            return 'history/process-instance/key/'.$this->key.$this->tenant();
        } else {
            return 'history/process-instance/'.$this->id;
        }
    }
}
