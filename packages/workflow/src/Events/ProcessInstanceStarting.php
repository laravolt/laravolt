<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;

class ProcessInstanceStarting
{
    use Dispatchable;

    public Module $module;

    public Form $form;

    /**
     * TaskSubmitting constructor.
     */
    public function __construct(Module $module, Form $form)
    {
        $this->module = $module;
        $this->form = $form;
    }
}
