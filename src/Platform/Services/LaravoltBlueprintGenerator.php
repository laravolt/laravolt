<?php

namespace Laravolt\Platform\Services;

use Blueprint\Contracts\Generator;
use Blueprint\Tree;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravoltBlueprintGenerator implements Generator
{
    private Filesystem $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function output(Tree $tree): array
    {
        // TODO: Implement output() method.
    }

    public function types(): array
    {
        return ['laravolt'];
    }
}
