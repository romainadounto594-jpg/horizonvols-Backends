<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'civility',
        'first_name',
        'last_name',
        'birth_date',
        'nationality',
        'gender',
        'document_type',
        'document_number',
        'document_expiry',
        'seat_number',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date:Y-m-d',
            'document_expiry' => 'date:Y-m-d',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->civility} {$this->first_name} {$this->last_name}";
    }
}