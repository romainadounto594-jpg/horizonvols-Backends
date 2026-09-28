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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            
            $table->string('civility', 10)->default('M.'); // M., Mme
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('gender', 1)->default('M'); // M, F
            
            // Pièce d'identité (Passeport / CNI)
            $table->string('document_type')->default('Passeport');
            $table->string('document_number');
            $table->date('document_expiry');
            
            // Siège sélectionné
            $table->string('seat_number', 5)->nullable()->default('12A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
