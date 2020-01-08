<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

class BooleanAdapter extends StringAdapter
{
    protected $type = 'checkbox';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['value'] = 1;
        $schema['checked'] = $this->value == 1;

        return $schema;
    }
}
