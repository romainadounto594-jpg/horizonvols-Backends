<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'seat_number',
        'cabin_class',
        'type',
        'is_exit_row',
        'is_available',
        'extra_price',
    ];

    protected function casts(): array
    {
        return [
            'is_exit_row' => 'boolean',
            'is_available' => 'boolean',
            'extra_price' => 'float',
        ];
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}