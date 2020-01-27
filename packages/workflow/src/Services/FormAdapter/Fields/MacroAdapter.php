<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class MacroAdapter extends FieldAdapter
{
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'name' => $this->field->field_name,
            'label' => $this->field->field_label,
            'hint' => $this->field->field_hint,
            'value' => $this->value,
            'readonly' => $this->readonly,
            'required' => in_array('required', $this->field->validation_rules),
            'validations' => $this->field->field_meta['validation'] ?? [],
            'placeholder' => $this->field->field_meta['placeholder'] ?? null,
            'attributes' => ['v-model' => $this->field->field_name],
            'fieldAttributes' => $this->attributes,
        ];
    }
}
