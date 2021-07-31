<?php

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
                .form()->make($this->schema)->render()
                .form()->action(form()->submit())
                .form()->close();
        };
    }
}
