<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'iata_code',
        'name',
        'city',
        'country',
        'region',
        'terminal_default',
        'timezone',
        'is_popular',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];
}