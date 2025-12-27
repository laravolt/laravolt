<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class UserStatus extends Enum
{
    public const string PENDING = 'PENDING';

    public const string ACTIVE = 'ACTIVE';

    public const string BLOCKED = 'BLOCKED';
}
