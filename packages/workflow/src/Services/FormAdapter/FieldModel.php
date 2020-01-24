<?php

namespace Laravolt\Workflow\Services\FormAdapter;

use Laravolt\Workflow\Models\CamundaForm;

class FieldModel extends CamundaForm
{
    protected $casts = [
        'field_meta' => 'array',
    ];
}
