<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfReading extends Model
{
    use HasFactory;

    protected $table = 'tblselfreading';

    protected $fillable = [
        'ID',
        'CA_NO',
        'Reading',
        'MM',
        'YYYY',
        'Reading_Date',
        'Status'
    ];
}
