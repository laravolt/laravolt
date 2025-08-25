<?php

namespace Laravolt\PrelineForm\OldInput;

interface OldInputInterface
{
    public function hasOldInput();

    public function getOldInput($key);
}
