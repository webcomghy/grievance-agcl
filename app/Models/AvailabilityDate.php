<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilityDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'from_date',
        'to_date',
    ];

}
