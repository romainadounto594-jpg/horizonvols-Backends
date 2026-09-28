<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'pnr',
        'user_id',
        'flight_id',
        'return_flight_id',
        'departure_date',
        'return_date',
        'status',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'base_price',
        'taxes',
        'extra_baggage_price',
        'insurance_price',
        'seat_price',
        'total_amount',
        'currency',
        'terminal',
        'gate',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date:Y-m-d',
            'return_date' => 'date:Y-m-d',
            'base_price' => 'float',
            'taxes' => 'float',
            'extra_baggage_price' => 'float',
            'insurance_price' => 'float',
            'seat_price' => 'float',
            'total_amount' => 'float',
        ];
    }

    /**
     * Génération automatique et sécurisée d'un code PNR unique (ex: HZV-4B7E2)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->pnr)) {
                $booking->pnr = 'HZV-' . strtoupper(Str::random(5));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function returnFlight()
    {
        return $this->belongsTo(Flight::class, 'return_flight_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function options()
    {
        return $this->hasOne(BookingOption::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}