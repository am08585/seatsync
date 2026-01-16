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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theater_id')->constrained()->cascadeOnDelete();
            $table->string('row');
            $table->integer('number');
            $table->integer('price_modifier')->default(0);
            $table->enum('seat_type', ['standard', 'vip', 'premium', 'wheelchair'])->default('standard');
            $table->timestamps();
            $table->unique(['theater_id', 'row', 'number']);
            $table->index('theater_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
