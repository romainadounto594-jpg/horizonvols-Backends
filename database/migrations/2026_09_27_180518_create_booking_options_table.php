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
        Schema::create('booking_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->integer('extra_baggage_count')->default(0);
            $table->string('meal_preference')->nullable()->default('Standard'); // Halal, Végétarien, etc.
            $table->string('insurance_plan')->nullable(); // Ex: Pack Sérénité Horizon
            $table->boolean('has_priority_boarding')->default(false);
            $table->boolean('has_lounge_access')->default(false);
            $table->boolean('has_flexible_ticket')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_options');
    }
};
