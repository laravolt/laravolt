<?php

namespace Laravolt\Workflow\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Laravolt\Workflow\Events\SharWorkflowInstanceCompleted;
use Laravolt\Workflow\Notifications\SharWorkflowCompletedNotification;

class SendWorkflowCompletionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SharWorkflowInstanceCompleted $event): void
    {
        $instance = $event->instance;

        // Get the user who created the instance
        if ($instance->created_by) {
            $user = \App\Models\User::find($instance->created_by);
            
            if ($user) {
                $user->notify(new SharWorkflowCompletedNotification($instance));
            }
        }

        // You could also send notifications to other users based on business logic
        // For example, notify workflow administrators, team members, etc.
        
        \Log::info('Workflow completion notification sent', [
            'instance_id' => $instance->id,
            'workflow_name' => $instance->workflow_name,
            'notified_user' => $instance->created_by,
        ]);
    }
}