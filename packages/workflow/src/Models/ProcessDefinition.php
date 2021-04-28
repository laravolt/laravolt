<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravolt\Suitable\AutoSort;

class ProcessDefinition extends Model
{
    use AutoSort;

    protected $table = 'wf_process_definitions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function getPresentTitleAttribute()
    {
        return Str::of($this->name ?? $this->key)->title()->replace('_', ' ');
    }
}
