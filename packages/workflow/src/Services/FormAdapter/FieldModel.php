<?php

namespace Laravolt\Workflow\Services\FormAdapter;

use Laravolt\Workflow\CamundaForm;

class FieldModel extends CamundaForm
{
    protected $casts = [
        'field_meta' => 'array',
    ];
}
