<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;

class Bpmn extends Model
{
    protected $table = 'workflow_bpmn';

    protected $guarded = [];
}
