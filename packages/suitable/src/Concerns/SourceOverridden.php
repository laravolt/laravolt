<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Concerns;

trait SourceOverridden
{
    protected $overriddenSource;

    public function source($source): self
    {
        $this->overriddenSource = $source;

        return $this;
    }
}
