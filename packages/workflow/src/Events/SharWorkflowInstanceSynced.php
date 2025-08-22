<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SharWorkflowInstanceSynced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SharWorkflowInstance $instance,
        public string $previousStatus,
        public string $newStatus
    ) {}
}