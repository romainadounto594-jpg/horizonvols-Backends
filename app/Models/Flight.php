<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline_id',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
        'duration_minutes',
        'stops',
        'stop_details',
        'aircraft',
        'cabin_class',
        'base_price',
        'taxes',
        'baggage_cabin',
        'baggage_hold',
        'ticket_policy',
        'is_refundable',
        'services',
        'seats_total',
        'seats_available',
        'is_recommended',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stop_details' => 'array',
            'services' => 'array',
            'base_price' => 'float',
            'taxes' => 'float',
            'is_refundable' => 'boolean',
            'is_recommended' => 'boolean',
            'is_active' => 'boolean',
            'duration_minutes' => 'integer',
            'stops' => 'integer',
            'seats_available' => 'integer',
            'seats_total' => 'integer',
        ];
    }

    // Relations Eloquent
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Formatage automatique de la durée (ex: "5h 55m")
    public function getDurationFormattedAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        return "{$hours}h " . str_pad($minutes, 2, '0', STR_PAD_LEFT) . "m";
    }

    // Calcul du prix total avec taxes
    public function getTotalPriceAttribute(): float
    {
        return $this->base_price + $this->taxes;
    }
}