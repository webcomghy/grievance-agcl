<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Consumer extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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
