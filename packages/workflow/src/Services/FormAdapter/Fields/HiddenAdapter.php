<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

class HiddenAdapter extends StringAdapter
{
    protected $type = 'hidden';

    public function toArray()
    {
        return [
            'type' => $this->type,
            'name' => $this->field->field_name,
            'label' => $this->readonly ? $this->field->field_label : '',
            'hint' => '',
            'value' => $this->value,
            'readonly' => $this->readonly,
            'validations' => [],
            'attributes' => ['v-model' => $this->field->field_name],
            'fieldAttributes' => $this->attributes,
        ];
    }
}
