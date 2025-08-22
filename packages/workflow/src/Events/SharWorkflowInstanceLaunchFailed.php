<?php

namespace Laravolt\Workflow\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SharWorkflowInstanceLaunchFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $workflowName,
        public array $variables,
        public string $errorMessage,
        public ?int $createdBy = null
    ) {}
}