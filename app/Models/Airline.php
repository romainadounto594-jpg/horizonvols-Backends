<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'alliance',
        'country',
        'logo_color',
        'accent_color',
        'rating',
        'baggage_policy',
        'fleet',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'fleet' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}