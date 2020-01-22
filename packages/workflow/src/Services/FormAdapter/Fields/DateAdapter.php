<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

class DateAdapter extends StringAdapter
{
    protected $type = 'date';

    public function toArray()
    {
        $data = parent::toArray();
        $data['attributes']['class'] = $this->field->field_meta['class'] ?? null;

        return $data;
    }
}
