<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravolt\Support\Traits\AutoFilter;
use Laravolt\Support\Traits\AutoSort;

class User extends \Laravolt\Platform\Models\User
{
    use Notifiable;
    use AutoSort;
    use AutoFilter;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $fillable = ['name', 'email', 'username', 'password', 'status', 'timezone'];
}
