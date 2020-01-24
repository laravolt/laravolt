<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class AutofillAdapter extends FieldAdapter
{
    public function toArray()
    {
        return [
            'type' => 'action',
            'items' => [
                [
                    'type' => 'hidden',
                    'label' => $this->field->field_name,
                    'name' => null,
                    'attributes' => [
                        'v-model' => $this->field->field_name,
                    ],
                ],
                [
                    'type' => 'button',
                    'name' => $this->value,
                    'label' => '<i class="icon search"></i>'.$this->field->field_label,
                    'value' => $this->value,
                    'fieldAttributes' => $this->attributes,
                    'attributes' => [
                        'data-remote-url' => route(
                            'workflow::table.index',
                            ['namespace' => $this->field->field_meta['table'] ?? null]
                        ),
                    ],
                ],
            ],
        ];
    }
}
