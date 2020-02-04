<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Laravolt\Camunda\Models\ProcessInstance;

class ProcessCompleted
{
    use SerializesModels;

    /**
     * @var ProcessInstance
     */
    public $processInstance;

    /**
     * @var Model
     */
    public $user;

    /**
     * ProcessStarted constructor.
     */
    public function __construct(ProcessInstance $processInstance, Model $user)
    {
        $this->processInstance = $processInstance;
        $this->user = $user;
    }
}
