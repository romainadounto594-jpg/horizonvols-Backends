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
        Schema::create('flight_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
            $table->string('seat_number', 5); // Ex: 12A, 14C
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first'])->default('economy');
            $table->enum('type', ['window', 'aisle', 'middle'])->default('window'); // Hublot, Couloir, Milieu
            $table->boolean('is_exit_row')->default(false); // Issue de secours (+ d'espace jambes)
            $table->boolean('is_available')->default(true);
            $table->decimal('extra_price', 8, 2)->default(0.00); // Ex: 0€ standard, 15€ hublot, 30€ issue
            $table->timestamps();

            $table->unique(['flight_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_seats');
    }
};
