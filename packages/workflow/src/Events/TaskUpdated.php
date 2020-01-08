<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Laravolt\Camunda\Models\TaskHistory;
use Laravolt\Workflow\Entities\Payload;

class TaskUpdated
{
    use SerializesModels;

    /**
     * @var TaskHistory
     */
    public $task;

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
    public function __construct(TaskHistory $task, Payload $payload, Model $user)
    {
        $this->task = $task;
        $this->payload = $payload;
        $this->user = $user;
    }
}
