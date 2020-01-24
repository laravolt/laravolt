<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Workflow\Enum\JenisTemplateSurat;

class TemplateSurat extends Model
{
    protected $table = 'template_surat';

    protected $fillable =
        [
            'name',
            'body',
            'process_definition_key',
            'type',
        ];

    public function getColorAttribute()
    {
        switch ($this->type) {
            case JenisTemplateSurat::STANDARD:
                return 'green';

                break;
            case JenisTemplateSurat::JASPER_PATH:
                return 'blue';

                break;
        }

        return '';
    }
}
