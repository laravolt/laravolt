<?php

return [
    'Workflow' => [
        'order' => 98,
        'menu' => [
            'BPMN' => [
                'route' => 'workflow::definitions.index',
                'active' => 'workflow/definitions/*',
                'icon' => 'code-branch',
                'permissions' => [\Laravolt\Platform\Enums\Permission::MANAGE_WORKFLOW],
            ],
        ],
    ],
];
