<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessInstance extends Model
{
    protected $table = 'wf_process_instances';

    public $incrementing = false;

    protected $keyType = 'string';
}
