<?php

declare(strict_types=1);

namespace Laravolt\Menu\Enum;

use BenSampo\Enum\Enum;

final class UrlType extends Enum
{
    public const INTERNAL = 'INTERNAL';

    public const EXTERNAL = 'EXTERNAL';
}
