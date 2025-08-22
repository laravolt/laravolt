<?php

namespace Laravolt\Workflow\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravolt\Workflow\Models\SharWorkflowInstance;

class SharWorkflowCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SharWorkflowInstance $instance
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $duration = $this->instance->getDurationInSeconds();
        $durationText = $duration ? "in {$duration} seconds" : '';

        return (new MailMessage)
            ->subject("Workflow Instance Completed: {$this->instance->workflow_name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your workflow instance has been completed successfully {$durationText}.")
            ->line("**Workflow:** {$this->instance->workflow_name}")
            ->line("**Instance ID:** {$this->instance->id}")
            ->line("**Status:** " . ucfirst($this->instance->status))
            ->line("**Started:** {$this->instance->started_at->format('Y-m-d H:i:s')}")
            ->line("**Completed:** {$this->instance->completed_at->format('Y-m-d H:i:s')}")
            ->action('View Instance Details', url("/workflow/shar/instances/{$this->instance->id}"))
            ->line('Thank you for using our workflow system!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shar_workflow_completed',
            'instance_id' => $this->instance->id,
            'workflow_name' => $this->instance->workflow_name,
            'status' => $this->instance->status,
            'duration_seconds' => $this->instance->getDurationInSeconds(),
            'completed_at' => $this->instance->completed_at,
            'tracking_code' => $this->instance->getTrackingCode(),
        ];
    }
}