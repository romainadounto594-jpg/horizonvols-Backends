<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Booking;

class PaymentController extends Controller
{
    /**
     * Initialise la clé secrète Stripe
     */
    public function __construct()
    {
        $stripeSecret = config('services.stripe.secret');
        Stripe::setApiKey($stripeSecret);
    }

    /**
     * Crée une intention de paiement (PaymentIntent) pour Stripe Elements
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'pnr' => 'nullable|string',
            'customer_email' => 'nullable|email',
        ]);

        $amount = (float) $request->amount;
        $currency = strtolower($request->input('currency', 'eur'));

        // Stripe gère différemment les devises à 0 décimale (comme le Franc CFA XOF)
        $zeroDecimalCurrencies = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];
        $isZeroDecimal = in_array($currency, $zeroDecimalCurrencies);

        $stripeAmount = $isZeroDecimal ? (int) round($amount) : (int) round($amount * 100);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $stripeAmount,
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'booking_pnr' => $request->pnr ?? 'PENDING',
                    'customer_email' => $request->customer_email ?? 'guest@horizonvols.com',
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => strtoupper($currency),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de la session de paiement : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirme et vérifie le paiement auprès de Stripe, puis valide la réservation
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'booking_id' => 'nullable|integer',
        ]);

        try {
            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status === 'succeeded') {
                // Si un ID de réservation est fourni, on valide son statut
                if ($request->booking_id) {
                    $booking = Booking::find($request->booking_id);
                    if ($booking) {
                        $booking->payment_status = 'paid';
                        $booking->status = 'confirmed';
                        $booking->stripe_payment_id = $intent->id;
                        $booking->save();
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Paiement Stripe validé avec succès.',
                    'payment_status' => 'paid',
                    'transaction_id' => $intent->id,
                ]);
            }

            return response()->json([
                'status' => 'pending',
                'message' => 'Le paiement est en cours de traitement par Stripe.',
                'payment_status' => $intent->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de vérifier la transaction : ' . $e->getMessage()
            ], 500);
        }
    }
}