<?php

namespace Laravolt\Workflow\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Segments extends Model
{
    protected $table = 'segments';

    protected $searchable = [
        'process_definition_key',
        'task_name',
        'segment_name',
        'segment_order',
    ];

    protected $fillable = [
        'process_definition_key',
        'task_name',
        'segment_name',
        'segment_order',
    ];

    public function scopeSearch(Builder $query, $keyword)
    {
        if ($keyword) {
            $query->whereLike($this->searchable, $keyword);
        }
    }
}
