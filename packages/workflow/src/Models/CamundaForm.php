<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;

class CamundaForm extends Model
{
    use AutoFilter;

    protected $table = 'camunda_form';

    protected $searchable = [
        'process_definition_key',
        'task_name',
        'form_name',
        'field_name',
        'field_type',
        'field_label',
        'field_order',
        'field_select_query',
        'field_meta',
    ];

    protected $fillable = [
        'process_definition_key',
        'task_name',
        'form_name',
        'field_name',
        'field_type',
        'field_hint',
        'field_label',
        'field_order',
        'field_select_query',
        'field_meta',
    ];

    public function scopeSearch(Builder $query, $keyword)
    {
        if ($keyword) {
            $query->whereLike($this->searchable, $keyword);
        }
    }
}
