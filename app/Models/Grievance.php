<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grievance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consumer_no',
        'ca_number',
        'category',
        'name',
        'address',
        'phone',
        'email',
        'description',
        'status',
    ];
    
    public static $categories = [
        'Bill Related',
        'Payment Related',
        'Additional Connection',
        'Disconnection - Temporary',
        'Disconnection - Permanent',
        'Name Change',
        'Mobile No Change',
        'Email Change',
        'Gas Leakage',
        'Others',
    ];
}
