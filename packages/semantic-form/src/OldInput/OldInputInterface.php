<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\OldInput;

interface OldInputInterface
{
    public function hasOldInput();

    public function getOldInput($key);
}
