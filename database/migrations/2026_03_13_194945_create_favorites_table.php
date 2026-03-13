<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic-like: local vehicle OR external auction car
            $table->foreignId('vehicle_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('external_vehicle_id')->nullable(); // auction API reference
            $table->string('vehicle_source'); // 'company' | 'third_party' | 'auction'

            $table->timestamps();

            $table->unique(['user_id', 'vehicle_id', 'external_vehicle_id'], 'unique_favorite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
