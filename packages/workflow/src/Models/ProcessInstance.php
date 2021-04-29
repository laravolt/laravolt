<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Workflow\Models\Collections\TaskCollection;
use Laravolt\Workflow\Models\Collections\VariableCollection;

class ProcessInstance extends Model
{
    protected $table = 'wf_process_instances';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tasks' => TaskCollection::class,
        'variables' => VariableCollection::class,
    ];

    public function getPermalinkAttribute()
    {
        return route("workflow::instances.show", $this->id);
    }
}
