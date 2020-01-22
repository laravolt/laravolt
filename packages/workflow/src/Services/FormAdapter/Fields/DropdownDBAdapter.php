<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class DropdownDBAdapter extends FieldAdapter
{
    protected $type = 'dropdownDB';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['query'] = $this->field->field_select_query;
        $schema['dependency'] = $this->field->field_meta['dependency'] ?? null;

        return $schema;
    }
}
