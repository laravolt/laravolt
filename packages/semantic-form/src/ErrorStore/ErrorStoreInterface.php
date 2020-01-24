<?php

namespace Laravolt\SemanticForm\ErrorStore;

interface ErrorStoreInterface
{
    public function hasError($key);

    public function getError($key);
}
