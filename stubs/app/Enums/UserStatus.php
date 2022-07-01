<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserStatus extends Enum
{
    const PENDING = 'PENDING';

    const ACTIVE = 'ACTIVE';

    const BLOCKED = 'BLOCKED';
}
