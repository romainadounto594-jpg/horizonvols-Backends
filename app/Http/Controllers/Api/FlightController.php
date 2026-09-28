<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\FlightSeat;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    /**
     * Liste des vols (appelé par Route::get('/flights', ...))
     */
    public function index(Request $request)
    {
        return $this->search($request);
    }

    /**
     * Recherche avancée de vols
     */
    public function search(Request $request)
    {
        $query = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('is_active', true);

        // Filtre départ
        if ($from = $request->input('from')) {
            $query->whereHas('departureAirport', function ($q) use ($from) {
                $q->where('iata_code', $from)
                  ->orWhere('city', 'LIKE', "%{$from}%");
            });
        }

        // Filtre arrivée
        if ($to = $request->input('to')) {
            $query->whereHas('arrivalAirport', function ($q) use ($to) {
                $q->where('iata_code', $to)
                  ->orWhere('city', 'LIKE', "%{$to}%");
            });
        }

        // Filtre classe
        if ($cabinClass = $request->input('cabin_class')) {
            if ($cabinClass !== 'all') {
                $query->where('cabin_class', $cabinClass);
            }
        }

        // Filtre direct / escales
        if ($request->has('stops') && $request->input('stops') !== 'all') {
            $stops = (int) $request->input('stops');
            if ($stops === 0) {
                $query->where('stops', 0);
            } else {
                $query->where('stops', '>=', 1);
            }
        }

        // Filtre prix max
        if ($maxPrice = $request->input('max_price')) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        // Tri (prix croissant, durée, recommandation)
        $sortBy = $request->input('sort_by', 'recommended');
        if ($sortBy === 'price_asc') {
            $query->orderBy('base_price', 'asc');
        } elseif ($sortBy === 'duration') {
            $query->orderBy('duration_minutes', 'asc');
        } else {
            $query->orderBy('is_recommended', 'desc')->orderBy('base_price', 'asc');
        }

        $flights = $query->get()->map(function ($flight) {
            $flight->duration_formatted = $flight->duration_formatted;
            $flight->total_price = $flight->total_price;
            return $flight;
        });

        return response()->json([
            'status' => 'success',
            'count' => $flights->count(),
            'data' => $flights,
        ]);
    }

    /**
     * Détails d'un vol spécifique avec ses sièges disponibles
     */
    public function show($id)
    {
        $flight = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->findOrFail($id);

        $seats = FlightSeat::where('flight_id', $flight->id)
            ->orderBy('seat_number', 'asc')
            ->get();

        $flight->seats = $seats;
        $flight->duration_formatted = $flight->duration_formatted;
        $flight->total_price = $flight->total_price;

        return response()->json([
            'status' => 'success',
            'data' => $flight
        ]);
    }
}