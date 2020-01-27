<?php

namespace Laravolt\Platform\Enums;

use BenSampo\Enum\Enum;

final class Permission extends Enum
{
    const MANAGE_USER = 'laravolt::manage-user';

    const MANAGE_ROLE = 'laravolt::manage-role';

    const MANAGE_PERMISSION = 'laravolt::manage-permission';

    const MANAGE_APPLICATION_LOG = 'laravolt::manage-application-log';

    const MANAGE_DB_BACKUP = 'laravolt::manage-database-backup';

    const MANAGE_MENU = 'laravolt::manage-menu';

    const MANAGE_WORKFLOW = 'laravolt::manage-workflow';

    const MANAGE_LOOKUP = 'laravolt::manage-lookup';
}
