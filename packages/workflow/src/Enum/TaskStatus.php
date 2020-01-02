<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Enum;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class TaskStatus extends Enum implements LocalizedEnum
{
    const DRAFT = 'DRAFT';

    const NEW = 'NEW';

    const ASSIGNED = 'ASSIGNED';

    const UNASSIGNED = 'UNASSIGNED';

    const COMPLETED = 'COMPLETED';
}
