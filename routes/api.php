<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AirportController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| 1. ROUTES PUBLIQUES (Visiteurs & Voyageurs)
|--------------------------------------------------------------------------
*/
// Authentification
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Consultation des aéroports et des vols
Route::get('/airports', [AirportController::class, 'index']);
Route::get('/flights', [FlightController::class, 'index']);
Route::get('/flights/{id}', [FlightController::class, 'show']);

// Recherche de billet par référence PNR
Route::get('/bookings/{pnr}', [BookingController::class, 'show']);

// Paiement sécurisé Stripe
Route::post('/payments/create-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);


/*
|--------------------------------------------------------------------------
| 2. ROUTES CLIENT PROTÉGÉES (Voyageurs connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Profil et session voyageur
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Réservations du client
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/user/bookings', [BookingController::class, 'userBookings']);
});


/*
|--------------------------------------------------------------------------
| 3. ROUTES ADMINISTRATEUR (Réservé au Back-Office HorizonVols)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Statistiques globales du tableau de bord
    Route::get('/stats', [AdminController::class, 'stats']);

    // Gestion complète des vols par l'admin
    Route::get('/flights', [AdminController::class, 'flights']);
    Route::post('/flights', [FlightController::class, 'store']);
    Route::put('/flights/{id}', [FlightController::class, 'update']);
    Route::delete('/flights/{id}', [FlightController::class, 'destroy']);

    // Gestion de toutes les réservations
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
});