<?php

namespace Laravolt\Camunda\Services\FormAdapter\Fields;

use Laravolt\Camunda\Services\FormAdapter\FieldAdapter;

class ScoreAdapter extends FieldAdapter
{
    protected $type = 'text';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['readonly'] = true;
        $schema['attributes'] = [
            'v-model' => $this->field->field_name,
            'data-role' => 'score',
        ];

        return $schema;
    }
}
