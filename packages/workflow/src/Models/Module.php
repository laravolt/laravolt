<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class Module extends Model
{
    use AutoFilter;
    use AutoSearch;
    use AutoSort;

    protected $table = 'wf_modules';

    protected $guarded = [];
}
