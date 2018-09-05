<?php

namespace Laravolt\Epilog;

use MyCLabs\Enum\Enum;

class Permission extends Enum
{
    const VIEW_BACKUP = 'cockpit::view-backup';
    const VIEW_LOG = 'cockpit::view-log';
}
