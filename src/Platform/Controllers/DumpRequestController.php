<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

/**
 * Echoes the submitted request for the playground forms. A debugging aid, so it
 * only answers while APP_DEBUG is on.
 */
class DumpRequestController
{
    public function __invoke()
    {
        abort_unless((bool) config('app.debug'), 404);

        dd(request()->all(), request()->allFiles());
    }
}
