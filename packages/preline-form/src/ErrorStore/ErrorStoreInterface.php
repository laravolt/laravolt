<?php

namespace Laravolt\PrelineForm\ErrorStore;

interface ErrorStoreInterface
{
    public function hasError($key);

    public function getError($key);
}
