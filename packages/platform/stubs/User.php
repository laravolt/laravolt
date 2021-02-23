<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSort;

class User extends \Laravolt\Platform\Models\User
{
    use HasFactory;
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
