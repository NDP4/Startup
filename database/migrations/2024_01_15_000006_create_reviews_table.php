<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('bus_rating');
            $table->text('bus_comment')->nullable();
            $table->foreignId('crew_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('crew_rating')->nullable();
            $table->text('crew_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
