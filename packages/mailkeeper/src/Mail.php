<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use SoftDeletes;

    protected $table = 'mail';

    protected $casts = [
        'from' => 'array',
        'to'   => 'array',
    ];

    protected $guarded = [];
}
