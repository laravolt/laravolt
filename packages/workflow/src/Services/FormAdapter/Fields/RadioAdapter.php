<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

class RadioAdapter extends DropdownAdapter
{
    protected $type = 'radioGroup';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['options'] = (array) json_decode($this->field->field_select_query, true);

        return $schema;
    }
}
