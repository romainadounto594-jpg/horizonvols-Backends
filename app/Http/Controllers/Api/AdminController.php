<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Métriques et statistiques clés pour le tableau de bord Admin
     */
    public function stats()
    {
        $totalRevenue = Payment::where('status', 'PAID')->sum('amount');
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'CONFIRMED')->count();
        $totalFlights = Flight::where('is_active', true)->count();
        $totalUsers = User::count();

        // 10 dernières réservations
        $recentBookings = Booking::with(['flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'stats' => [
                'total_revenue' => (float) $totalRevenue,
                'total_bookings' => $totalBookings,
                'confirmed_bookings' => $confirmedBookings,
                'total_flights' => $totalFlights,
                'total_users' => $totalUsers,
            ],
            'recent_bookings' => $recentBookings,
        ]);
    }

    /**
     * Liste complète des vols avec filtres pour l'admin
     */
    public function flights()
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $flights,
        ]);
    }
}