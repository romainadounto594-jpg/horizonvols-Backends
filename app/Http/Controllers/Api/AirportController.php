<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    /**
     * Liste tous les aéroports ou filtre par recherche (autocomplétion)
     */
    public function index(Request $request)
    {
        $query = Airport::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('city', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('iata_code', 'LIKE', "%{$search}%")
                  ->orWhere('country', 'LIKE', "%{$search}%");
            });
        }

        $airports = $query->orderBy('is_popular', 'desc')
                          ->orderBy('city', 'asc')
                          ->get();

        return response()->json([
            'status' => 'success',
            'data' => $airports
        ]);
    }
}