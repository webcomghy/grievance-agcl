<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Consumer extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'mobile_number',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
