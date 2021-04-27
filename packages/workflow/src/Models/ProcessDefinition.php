<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoSort;

class ProcessDefinition extends Model
{
    use AutoSort;

    protected $table = 'wf_process_definitions';

    public $incrementing = false;

    protected $keyType = 'string';
}
