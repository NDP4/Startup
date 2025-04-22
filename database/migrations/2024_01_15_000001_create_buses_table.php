<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number_plate')->unique();
            $table->text('description');
            $table->integer('default_seat_capacity');
            $table->enum('status', ['available', 'maintenance', 'booked'])->default('available');
            $table->json('images')->nullable()->comment('Array of image objects with url and description');
            $table->json('image_names')->nullable();
            $table->enum('pricing_type', ['daily', 'distance'])->default('daily');
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('price_per_km', 10, 2)->nullable();
            $table->decimal('legrest_price_per_seat', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
