<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Form extends Component
{
    public array $schema;

    public function __construct(array $schema = [])
    {
        $this->schema = $schema;
    }

    public function render()
    {
        return function ($data) {
            return form()
                ->open($data['attributes']['action'] ?? '')
                ->setMethod($data['attributes']['method'] ?? 'POST')
                ->horizontal()
                .$data['slot']
                .form()->make($this->schema)->render()
                .form()->action(form()->submit())
                .form()->close();
        };
    }
}
