<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number', 10); // Ex: AF 718, HC 404
            
            // Clés étrangères vers les compagnies et aéroports
            $table->foreignId('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->foreignId('departure_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('arrival_airport_id')->constrained('airports')->onDelete('cascade');
            
            // Horaires et durée
            $table->time('departure_time');      // Ex: 15:40:00
            $table->time('arrival_time');        // Ex: 19:35:00
            $table->integer('duration_minutes'); // Ex: 355 (soit 5h 55m)
            $table->integer('stops')->default(0); // 0 = Direct, 1 = 1 escale...
            $table->json('stop_details')->nullable(); // Détails de l'escale si applicable

            // Appareil et classe
            $table->string('aircraft')->default('Boeing 777-300ER');
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first'])->default('economy');
            
            // Tarification
            $table->decimal('base_price', 10, 2); // Ex: 489.00
            $table->decimal('taxes', 10, 2)->default(78.00);
            
            // Bagages et conditions
            $table->string('baggage_cabin')->default('1x 12kg');
            $table->string('baggage_hold')->default('2x 23kg inclus');
            $table->string('ticket_policy')->default('Modifiable sous conditions');
            $table->boolean('is_refundable')->default(false);
            
            // Services inclus et disponibilité
            $table->json('services')->nullable(); // Ex: ["Wi-Fi", "Repas chaud", "Prise USB"]
            $table->integer('seats_total')->default(180);
            $table->integer('seats_available')->default(24);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour accélérer la recherche de vols par les clients
            $table->index(['departure_airport_id', 'arrival_airport_id']);
            $table->index('cabin_class');
            $table->index('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
