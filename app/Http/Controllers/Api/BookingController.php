<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Payment;
use App\Models\FlightSeat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Création d'une nouvelle réservation (Tunnel de commande)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'return_flight_id' => 'nullable|exists:flights,id',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date',
            
            // Contact
            'customer_first_name' => 'required|string|max:100',
            'customer_last_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:30',
            
            // Passagers (tableau)
            'passengers' => 'required|array|min:1',
            'passengers.*.civility' => 'required|string',
            'passengers.*.first_name' => 'required|string|max:100',
            'passengers.*.last_name' => 'required|string|max:100',
            'passengers.*.birth_date' => 'required|date',
            'passengers.*.nationality' => 'required|string',
            'passengers.*.document_type' => 'required|string',
            'passengers.*.document_number' => 'required|string',
            'passengers.*.document_expiry' => 'required|date',
            'passengers.*.seat_number' => 'nullable|string',

            // Options
            'options.extra_baggage_count' => 'nullable|integer',
            'options.meal_preference' => 'nullable|string',
            'options.insurance_plan' => 'nullable|string',
            'options.has_priority_boarding' => 'nullable|boolean',
            'options.has_lounge_access' => 'nullable|boolean',

            // Paiement
            'payment_method' => 'required|string', // card, wave, orange_money, etc.
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $flight = Flight::findOrFail($validated['flight_id']);
            $passengerCount = count($validated['passengers']);

            // Calcul financier
            $basePrice = $flight->base_price * $passengerCount;
            $taxes = $flight->taxes * $passengerCount;
            
            $extraBaggageCount = $request->input('options.extra_baggage_count', 0);
            $extraBaggagePrice = $extraBaggageCount * 50.00; // 50€ par bagage supplémentaire
            
            $hasInsurance = !empty($request->input('options.insurance_plan'));
            $insurancePrice = $hasInsurance ? (39.00 * $passengerCount) : 0.00;
            
            $seatPrice = 0.00;
            foreach ($validated['passengers'] as $p) {
                if (!empty($p['seat_number'])) {
                    $seat = FlightSeat::where('flight_id', $flight->id)
                        ->where('seat_number', $p['seat_number'])
                        ->first();
                    if ($seat) {
                        $seatPrice += $seat->extra_price;
                        $seat->update(['is_available' => false]);
                    }
                }
            }

            $totalAmount = $basePrice + $taxes + $extraBaggagePrice + $insurancePrice + $seatPrice;

            // Déterminer l'ID de l'utilisateur s'il est connecté
            $userId = $request->user() ? $request->user()->id : null;

            // 1. Créer la réservation
            $booking = Booking::create([
                'user_id' => $userId,
                'flight_id' => $flight->id,
                'return_flight_id' => $validated['return_flight_id'] ?? null,
                'departure_date' => $validated['departure_date'],
                'return_date' => $validated['return_date'] ?? null,
                'status' => 'CONFIRMED',
                'customer_first_name' => $validated['customer_first_name'],
                'customer_last_name' => $validated['customer_last_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'base_price' => $basePrice,
                'taxes' => $taxes,
                'extra_baggage_price' => $extraBaggagePrice,
                'insurance_price' => $insurancePrice,
                'seat_price' => $seatPrice,
                'total_amount' => $totalAmount,
                'currency' => 'EUR',
                'terminal' => $flight->departureAirport->terminal_default ?? 'Terminal 2E',
                'gate' => 'G' . rand(12, 48),
            ]);

            // 2. Enregistrer chaque passager
            foreach ($validated['passengers'] as $pData) {
                Passenger::create([
                    'booking_id' => $booking->id,
                    'civility' => $pData['civility'],
                    'first_name' => $pData['first_name'],
                    'last_name' => $pData['last_name'],
                    'birth_date' => $pData['birth_date'],
                    'nationality' => $pData['nationality'],
                    'gender' => ($pData['civility'] === 'Mme' ? 'F' : 'M'),
                    'document_type' => $pData['document_type'],
                    'document_number' => $pData['document_number'],
                    'document_expiry' => $pData['document_expiry'],
                    'seat_number' => $pData['seat_number'] ?? 'Libre',
                ]);
            }

            // 3. Enregistrer les options
            BookingOption::create([
                'booking_id' => $booking->id,
                'extra_baggage_count' => $extraBaggageCount,
                'meal_preference' => $request->input('options.meal_preference', 'Standard'),
                'insurance_plan' => $request->input('options.insurance_plan', null),
                'has_priority_boarding' => (bool) $request->input('options.has_priority_boarding', false),
                'has_lounge_access' => (bool) $request->input('options.has_lounge_access', false),
            ]);

            // 4. Enregistrer la transaction de paiement simulée réussie
            Payment::create([
                'booking_id' => $booking->id,
                'transaction_reference' => 'TXN-' . strtoupper(Str::random(10)),
                'amount' => $totalAmount,
                'currency' => 'EUR',
                'payment_method' => $validated['payment_method'],
                'status' => 'PAID',
                'card_last4' => '4242',
                'card_brand' => 'Visa',
                'paid_at' => now(),
            ]);

            // Décrémenter les sièges disponibles sur le vol
            $flight->decrement('seats_available', $passengerCount);

            // Si client connecté, créditer des Miles de fidélité (1€ = 1 Mile)
            if ($request->user()) {
                $request->user()->increment('miles', round($totalAmount));
            }

            // Recharger les relations pour la réponse
            $booking->load(['flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'options', 'payment']);

            return response()->json([
                'status' => 'success',
                'message' => 'Réservation confirmée avec succès ! Votre billet électronique a été généré.',
                'booking' => $booking,
            ], 201);
        });
    }

    /**
     * Recherche d'une réservation par code PNR (Mes Réservations / Billet)
     */
    public function showByPnr($pnr)
    {
        $booking = Booking::with([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
            'options',
            'payment'
        ])->where('pnr', strtoupper($pnr))->firstOrFail();

        return response()->json([
            'status' => 'success',
            'booking' => $booking,
        ]);
    }

    /**
     * Liste des réservations de l'utilisateur connecté
     */
    public function myBookings(Request $request)
    {
        $bookings = Booking::with([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
            'options'
        ])
        ->where('user_id', $request->user()->id)
        ->orWhere('customer_email', $request->user()->email)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }
}