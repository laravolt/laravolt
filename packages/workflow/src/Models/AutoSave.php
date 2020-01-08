<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class AutoSave extends Model
{
    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'workflow_autosave';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];
}
