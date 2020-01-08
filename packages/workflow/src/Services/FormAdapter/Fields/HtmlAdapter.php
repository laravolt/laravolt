<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class HtmlAdapter extends FieldAdapter
{
    protected $type = 'html';

    public function toArray()
    {
        $schema = parent::toArray();
        $schema['content'] = $this->field->field_select_query;

        return $schema;
    }
}
