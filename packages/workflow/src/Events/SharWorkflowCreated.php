<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravolt\Workflow\Models\SharWorkflow;

class SharWorkflowCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public SharWorkflow $workflow,
        public array $sharResponse
    ) {}
}