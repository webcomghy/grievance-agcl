<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrievanceTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grievance_id',
        'status',
        'description',
        'assigned_to',
        'employee_id',
        'created_by',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class);
    }
}
