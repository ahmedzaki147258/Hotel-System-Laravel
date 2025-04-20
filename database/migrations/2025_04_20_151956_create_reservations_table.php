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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('restrict');
            //$table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->unsignedInteger('accompany_number');
            $table->unsignedBigInteger('paid_price_in_cents'); // Actual price paid
            $table->string('payment_id')->nullable(); // For Stripe payment ID
            $table->timestamp('check_out_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
