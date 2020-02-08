<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Enum;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class TaskStatus extends Enum implements LocalizedEnum
{
    /**
     * Task status dibuat berdasar task status dari Camunda, dengan tambahan:
     * - DRAFT (sudah disimpan di DB aplikasi, tapi belum dikirim ke Camunda)
     * For complete reference, please refer to
     * https://docs.camunda.org/manual/7.7/webapps/tasklist/task-lifecycle/.
     */
    const DRAFT = 'DRAFT';

    const NEW = 'NEW';

    const ASSIGNED = 'ASSIGNED';

    const UNASSIGNED = 'UNASSIGNED';

    const DELEGATED = 'DELEGATED';

    const COMPLETED = 'COMPLETED';

    const CANCELED = 'CANCELED';
}
