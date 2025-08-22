<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SharWorkflowInstanceLaunched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SharWorkflowInstance $instance,
        public array $sharResponse
    ) {}
}