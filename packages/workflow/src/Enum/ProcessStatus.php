<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Enum;

use BenSampo\Enum\Enum;

class ProcessStatus extends Enum
{
    const ACTIVE = 'ACTIVE';

    const SUSPENDED = 'SUSPENDED';

    const COMPLETED = 'COMPLETED';

    const EXTERNALLY_TERMINATED = 'EXTERNALLY_TERMINATED';

    const INTERNALLY_TERMINATED = 'INTERNALLY_TERMINATED';
}
