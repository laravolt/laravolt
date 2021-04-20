<?php

namespace Laravolt\Platform\Controllers;

class DumpRequestController
{
    public function __invoke()
    {
        dd(request()->all(), request()->allFiles());
    }
}
