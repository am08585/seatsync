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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screening_id')->constrained()->cascadeOnDelete();
            $table->integer('total_price');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('screening_id');
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
