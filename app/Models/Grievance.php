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
        'subcategory',
        'name',
        'address',
        'phone',
        'email',
        'description',
        'admin_remark',
        'file_path',
        'resolved_file_path',
        'status',
        'priority_score', 
        'longitude',
        'latitude',
        'grid_user',
        'grid_code',
        'consumer_id',
        'is_grid_admin',
        'ticket_number',
        'satisfaction',
        'satisfaction_remark',
    ];
    
    public static $categories = [
        'Gas Supply Related' => [
            'Fire/Leakage',
            'Cut',
            'Bubbles coming out from water bodies',
            'Gas connection not provided within 3 months from the date of application',
            'New connection required, Lat Long Auto-Capturing required',
            'Temporary disconnection required',
            'Shifting required',
            'Meter damage',
            'Meter not working',
            'Any Other issues',
        ],
        'Bill Related' => [
            'Bill not received',
            'High bill amount',
            'Wrong bill delivered',
            'Wrong bill amount',
            'How to download the bill. Option 1: Should direct to a self-help video',
            'Disconnected but bills are still generated',
            'Non receipt of bills',
            'Bills cannot be downloaded',
            'Any Other issues',
        ],
        'Payment Related' => [
            "Can't pay online",
            'What is the last date for bill payment?',
            'How to pay online? Option 1: Should direct to a self-help video',
            'Any Other issues',
        ],
        'KYC Related' => [
            'Change of ownership',
            'Mobile number and email update',
            'Any Other issues',
        ],
    ];

    public static $categories_priority = [
        'Bill Related' => 5,
        'Payment Related' => 6,
        'KYC Related' => 3,
        'Gas Supply Related' => 9,
    ];

    public static $subcategories_priority = [
        'Fire/Leakage' => 9,
        'Cut' => 9,
        'Bubbles coming out from water bodies' => 8,
        'Gas connection not provided within 3 months from the date of application' => 7,
        'New connection required, Lat Long Auto-Capturing required' => 5,
        'Temporary disconnection required' => 5,
        'Shifting required' => 4,
        'Meter damage' => 5,
        'Meter not working' => 5,
        'Any Other issues' => 3,
        'Bill not received' => 4,
        'High bill amount' => 3,
        'Wrong bill delivered' => 5,
        'Wrong bill amount' => 4,
        'How to download the bill. Option 1: Should direct to a self-help video' => 2,
        'Disconnected but bills are still generated' => 5,
        'Non receipt of bills' => 5,
        'Bills cannot be downloaded' => 5,
        "Can't pay online" => 5,
        'What is the last date for bill payment?' => 3,
        'How to pay online? Option 1: Should direct to a self-help video' => 3,
        'Change of ownership' => 5,
        'Mobile number and email update' => 3,
    ];

    public static $statuses = [
        'Pending',
        'Forwarded',
        'Assigned',
        'Resolved',
        'Closed',
        'Withdrawn',
        'Feedback',
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
