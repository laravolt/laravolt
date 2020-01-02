<?php

namespace Laravolt\Camunda\Models;

use Laravolt\Camunda\Enum\JenisTemplateSurat;
use Illuminate\Database\Eloquent\Model;

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
