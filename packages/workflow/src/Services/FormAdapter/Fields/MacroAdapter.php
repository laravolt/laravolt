<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class MacroAdapter extends FieldAdapter
{
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
