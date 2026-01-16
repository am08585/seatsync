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
        Schema::create('seat_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screening_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->string('hold_token');
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['screening_id', 'seat_id', 'user_id', 'hold_token']);
            $table->index('hold_token');
            $table->index('user_id');
            $table->index('screening_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_holds');
    }
};
