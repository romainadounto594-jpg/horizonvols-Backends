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
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('iata_code', 3)->unique(); // Ex: CDG, DSS, ABJ
            $table->string('name');                   // Ex: Paris-Charles de Gaulle
            $table->string('city');                   // Ex: Paris
            $table->string('country');                // Ex: France
            $table->string('region')->nullable();     // Ex: Europe, Afrique
            $table->string('terminal_default')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_popular')->default(false);
            $table->timestamps();

            $table->index('city');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
