<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravolt\Platform\Models\User as BaseUser;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

/**
 * @use HasFactory<UserFactory>
 */
class User extends BaseUser
{
    use AutoFilter, AutoSearch, AutoSort;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    // use \Laravel\Sanctum\HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'username', 'password', 'status', 'timezone'];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
