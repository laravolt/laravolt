<?php

namespace Laravolt\Epilog;

use MyCLabs\Enum\Enum;

class Permission extends Enum
{
    const VIEW_BACKUP = 'epilog::view-backup';
    const VIEW_LOG = 'epilog::view-log';
}
