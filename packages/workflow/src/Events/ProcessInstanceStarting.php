<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laravolt\Camunda\Dto\Task;
use Laravolt\Workflow\Entities\Form;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Models\ProcessInstance;

class ProcessInstanceStarting
{
    use Dispatchable;

    /**
     * @var \Laravolt\Workflow\Entities\Module
     */
    public Module $module;

    /**
     * @var \Laravolt\Workflow\Entities\Form
     */
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
