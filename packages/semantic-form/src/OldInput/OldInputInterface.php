<?php

namespace Laravolt\SemanticForm\OldInput;

interface OldInputInterface
{
    public function hasOldInput();

    public function getOldInput($key);
}
