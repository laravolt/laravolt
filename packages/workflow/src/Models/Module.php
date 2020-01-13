<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Module extends Model
{
    use Rememberable;

    protected $table = 'workflow_module';

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(config('laravolt.acl.models.role'), 'workflow_permission', 'module_id', 'role_id')
            ->withPivot('permission');
    }

    public function getIndexUrl()
    {
        return route('workflow::process.index', $this->key);
    }

    public function getCreateUrl()
    {
        return route('workflow::process.create', $this->key);
    }

    public function getBpmnUrl()
    {
        return route('workflow::process.bpmn', $this->key);
    }
}
