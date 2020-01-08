<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class DropdownAdapter extends FieldAdapter
{
    protected $type = 'dropdown';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['options'] = (array) json_decode($this->field->field_select_query, true);

        return $schema;
    }
}
