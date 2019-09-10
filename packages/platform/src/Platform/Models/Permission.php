<?php

namespace Laravolt\Platform\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'acl_permissions';

    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('orderByName', function (Builder $builder) {
            $builder->orderBy('name');
        });
    }
}
