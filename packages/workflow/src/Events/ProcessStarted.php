<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Laravolt\Camunda\Models\ProcessInstance;
use Laravolt\Workflow\Entities\Payload;

class ProcessStarted
{
    use SerializesModels;

    /**
     * @var ProcessInstance
     */
    public $processInstance;

    /**
     * @var Payload
     */
    public $payload;

    /**
     * @var Model
     */
    public $user;

    /**
     * ProcessStarted constructor.
     */
    public function __construct(ProcessInstance $processInstance, Payload $payload, Model $user)
    {
        $this->processInstance = $processInstance;
        $this->payload = $payload;
        $this->user = $user;
    }
}
