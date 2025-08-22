<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharWorkflow extends Model
{
    protected $table = 'wf_shar_workflows';

    protected $fillable = [
        'name',
        'bpmn_xml',
        'description',
        'version',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all instances for this workflow
     */
    public function instances(): HasMany
    {
        return $this->hasMany(SharWorkflowInstance::class, 'workflow_name', 'name');
    }

    /**
     * Get the latest version of this workflow
     */
    public static function getLatestVersion(string $name): ?self
    {
        return static::where('name', $name)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * Create a new version of the workflow
     */
    public static function createNewVersion(string $name, string $bpmnXml, ?string $description = null, ?int $createdBy = null): self
    {
        $latestVersion = static::getLatestVersion($name);
        $newVersion = $latestVersion ? $latestVersion->version + 1 : 1;

        return static::create([
            'name' => $name,
            'bpmn_xml' => $bpmnXml,
            'description' => $description,
            'version' => $newVersion,
            'status' => 'active',
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Check if workflow is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Deactivate this workflow
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Get workflow statistics
     */
    public function getStatistics(): array
    {
        $instances = $this->instances();
        
        return [
            'total_instances' => $instances->count(),
            'running_instances' => $instances->where('status', 'running')->count(),
            'completed_instances' => $instances->where('status', 'completed')->count(),
            'failed_instances' => $instances->where('status', 'failed')->count(),
        ];
    }
}