<?php

namespace Laravolt\Camunda\Services\FormAdapter;

use Laravolt\Camunda\CamundaForm;

class FieldModel extends CamundaForm
{
    protected $casts = [
        'field_meta' => 'array',
    ];
}
