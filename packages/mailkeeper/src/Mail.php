<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $table = 'mail';

    protected $casts = [
        'from' => 'array',
        'to'   => 'array',
    ];

    protected $guarded = [];
}
