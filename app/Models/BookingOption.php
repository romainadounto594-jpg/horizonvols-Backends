<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'extra_baggage_count',
        'meal_preference',
        'insurance_plan',
        'has_priority_boarding',
        'has_lounge_access',
        'has_flexible_ticket',
    ];

    protected function casts(): array
    {
        return [
            'extra_baggage_count' => 'integer',
            'has_priority_boarding' => 'boolean',
            'has_lounge_access' => 'boolean',
            'has_flexible_ticket' => 'boolean',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}