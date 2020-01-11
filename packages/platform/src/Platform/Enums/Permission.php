<?php

namespace Laravolt\Platform\Enums;

use BenSampo\Enum\Enum;

final class Permission extends Enum
{
    const MANAGE_USER = 'laravolt::manage-user';

    const MANAGE_ROLE = 'laravolt::manage-role';

    const MANAGE_PERMISSION = 'laravolt::manage-permission';
}
