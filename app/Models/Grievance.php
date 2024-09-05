<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grievance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consumer_no',
        'ca_no',
        'category',
        'name',
        'address',
        'phone',
        'email',
        'description',
        'status',
        'priority_score', // Change 'priority' to 'priority_score'
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

    public static $categories_priority = [
        'Bill Related' => 5,
        'Payment Related' => 6,
        'Additional Connection' => 3,
        'Disconnection - Temporary' => 5,
        'Disconnection - Permanent' => 5,
        'Name Change' => 3,
        'Mobile No Change' => 3,
        'Email Change' => 3,
        'Gas Leakage' => 9,
        'Others' => 3,
    ];

    public static $statuses = [
        'Pending',
        'Forwarded',
        'Resolved',
        'Closed',
    ];

    protected function priority(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $score = $attributes['priority_score'] ?? 0;
                if ($score >= 7) {
                    return 'High';
                } elseif ($score >= 4) {
                    return 'Medium';
                } else {
                    return 'Low';
                }
            }
        );
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(GrievanceTransaction::class);
    }
}
