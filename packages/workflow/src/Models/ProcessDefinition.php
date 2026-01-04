<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoSort;
use Throwable;

class ProcessDefinition extends Model
{
    use AutoSort;

    public $incrementing = false;

    protected $table = 'wf_process_definitions';

    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * @throws Throwable
     */
    public static function importFromCamunda(array $definitions)
    {
        DB::transaction(
            function () use ($definitions) {
                foreach ($definitions as $definition) {
                    self::firstOrCreate(
                        [
                            'id' => $definition->id,
                            'name' => $definition->name,
                            'key' => $definition->key,
                            'version' => $definition->version,
                        ]
                    );
                }
            }
        );
    }
}
