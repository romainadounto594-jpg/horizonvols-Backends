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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('transaction_reference')->unique(); // Ex: TXN-8934752934
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method'); // card, wave, orange_money, mtn, paypal
            $table->enum('status', ['PAID', 'PENDING', 'FAILED', 'REFUNDED'])->default('PAID');
            $table->string('card_last4', 4)->nullable();
            $table->string('card_brand')->nullable(); // Visa, Mastercard
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('transaction_reference');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
