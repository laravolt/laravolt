<?php

declare(strict_types=1);

namespace Laravolt\Media;

class MediaInputBag
{
    protected $key;

    /**
     * MediaInputBag constructor.
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function toArray(): array
    {
        collect(json_decode(request("_{$this->key}", '{}'), true))->flatten()->toArray();
    }

    public function store(): array
    {
    }

    public function delete(): bool
    {
    }
}
