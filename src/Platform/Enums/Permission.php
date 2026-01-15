<?php

declare(strict_types=1);

namespace Laravolt\Platform\Enums;

use BenSampo\Enum\Enum;

final class Permission extends Enum
{
    public const ANY = '*';

    public const MANAGE_USER = 'laravolt::manage-user';

    public const MANAGE_ROLE = 'laravolt::manage-role';

    public const MANAGE_PERMISSION = 'laravolt::manage-permission';

    public const MANAGE_APPLICATION_LOG = 'laravolt::manage-application-log';

    public const MANAGE_DB = 'laravolt::manage-database';

    public const MANAGE_LOOKUP = 'laravolt::manage-lookup';

    public const MANAGE_WORKFLOW = 'laravolt::manage-workflow';

    public const MANAGE_SETTINGS = 'laravolt::manage-settings';

    public const MANAGE_SYSTEM = 'laravolt::manage-system';

    public const MANAGE_SEO = 'laravolt::manage-seo';
}
