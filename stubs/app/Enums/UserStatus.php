<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class UserStatus extends Enum
{
    const PENDING = 'PENDING';

    const ACTIVE = 'ACTIVE';

    const BLOCKED = 'BLOCKED';
}
