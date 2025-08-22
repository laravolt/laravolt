<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharWorkflowInstance extends Model
{
    protected $table = 'wf_shar_workflow_instances';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'workflow_name',
        'status',
        'variables',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the workflow this instance belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(SharWorkflow::class, 'workflow_name', 'name');
    }

    /**
     * Check if instance is running
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Check if instance is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if instance has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark instance as completed
     */
    public function markCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark instance as failed
     */
    public function markFailed(string $reason = null): bool
    {
        $data = [
            'status' => 'failed',
            'completed_at' => now(),
        ];

        if ($reason) {
            $variables = $this->variables ?? [];
            $variables['failure_reason'] = $reason;
            $data['variables'] = $variables;
        }

        return $this->update($data);
    }

    /**
     * Update instance variables
     */
    public function updateVariables(array $variables): bool
    {
        $currentVariables = $this->variables ?? [];
        $mergedVariables = array_merge($currentVariables, $variables);

        return $this->update(['variables' => $mergedVariables]);
    }

    /**
     * Get a specific variable value
     */
    public function getVariable(string $key, $default = null)
    {
        return data_get($this->variables, $key, $default);
    }

    /**
     * Set a specific variable value
     */
    public function setVariable(string $key, $value): bool
    {
        $variables = $this->variables ?? [];
        $variables[$key] = $value;

        return $this->update(['variables' => $variables]);
    }

    /**
     * Get instance duration in seconds
     */
    public function getDurationInSeconds(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->completed_at ?? now();
        return $endTime->diffInSeconds($this->started_at);
    }

    /**
     * Get tracking code for this instance
     */
    public function getTrackingCode(): string
    {
        return $this->id;
    }
}