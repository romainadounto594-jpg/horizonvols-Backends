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
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // Ex: AF, HC, SS, ET, EK
            $table->string('name');              // Ex: Air France, Air Sénégal
            $table->string('alliance')->nullable(); // SkyTeam, Star Alliance, Indépendant
            $table->string('country');
            $table->string('logo_color')->default('#002157');
            $table->string('accent_color')->default('#3B82F6');
            $table->decimal('rating', 3, 1)->default(4.5);
            $table->text('baggage_policy')->nullable();
            $table->json('fleet')->nullable();   // Ex: ["Boeing 777-300ER", "Airbus A350"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};
