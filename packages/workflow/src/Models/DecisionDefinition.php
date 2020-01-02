<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class DecisionDefinition extends CamundaModel
{
    public function evaluate($variables)
    {
        return $this->post('evaluate', ['json' => compact('variables')]);
    }

    public function getDefinition()
    {
        return $this->get('');
    }
}
