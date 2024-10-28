<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterUpload extends Model
{
    use HasFactory;

    protected $table = 'tblmeterupload';

    protected $fillable = [
        'consumer_no',
        'phone_number',
        'yearMonth',
        'reading',
        'image',
        'longitude',
        'latitude',
    ];
    
}
