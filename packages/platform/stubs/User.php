<?php

namespace App;

use Illuminate\Notifications\Notifiable;

class User extends \Laravolt\Platform\Models\User
{
    use Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
