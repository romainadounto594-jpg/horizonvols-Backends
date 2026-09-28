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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('pnr', 12)->unique(); // Référence dossier passager ex: HZV-7842A
            
            // Client (nullable pour permettre aussi la réservation sans création de compte)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Vol aller et vol retour (optionnel)
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
            $table->foreignId('return_flight_id')->nullable()->constrained('flights')->nullOnDelete();
            
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            
            $table->enum('status', ['CONFIRMED', 'PENDING', 'CANCELLED', 'COMPLETED'])->default('CONFIRMED');
            
            // Coordonnées du contact principal
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            
            // Détail financier
            $table->decimal('base_price', 10, 2);
            $table->decimal('taxes', 10, 2);
            $table->decimal('extra_baggage_price', 10, 2)->default(0.00);
            $table->decimal('insurance_price', 10, 2)->default(0.00);
            $table->decimal('seat_price', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');

            // Embarquement indicatif
            $table->string('terminal')->nullable()->default('Terminal 2E');
            $table->string('gate')->nullable()->default('K42');
            
            $table->timestamps();

            $table->index('pnr');
            $table->index('status');
            $table->index('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
