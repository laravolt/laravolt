<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\OldInput;

interface OldInputInterface
{
    public function hasOldInput();

    public function getOldInput($key);
}
