<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\ErrorStore;

interface ErrorStoreInterface
{
    public function hasError($key);

    public function getError($key);
}
