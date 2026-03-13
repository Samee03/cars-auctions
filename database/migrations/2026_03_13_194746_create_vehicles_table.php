<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Source identification
            $table->string('source'); // 'company' | 'third_party'
            $table->string('stock_number')->nullable()->unique();
            $table->string('vin')->nullable();

            // Core fields
            $table->string('make');
            $table->string('model');
            $table->string('trim')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage')->nullable(); // in km
            $table->string('mileage_unit')->default('km'); // 'km' | 'miles'

            // Pricing (store in JPY as base, USD can be converted)
            $table->unsignedBigInteger('price_jpy');
            $table->unsignedBigInteger('price_usd')->nullable();

            // Condition & auction info
            $table->string('auction_grade')->nullable();
            $table->text('condition_report')->nullable();
            $table->text('seller_notes')->nullable();

            // Location
            $table->string('location')->nullable();

            // Status
            $table->string('status')->default('available'); // 'available' | 'sold' | 'reserved' | 'pending'

            // External reference
            $table->string('vehicle_url')->nullable();

            // Featured / visibility
            $table->boolean('is_featured')->default(false);
            $table->string('price_tier')->nullable(); // 'low' | 'mid' | 'high' (for featured sections)

            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering/sorting
            $table->index('source');
            $table->index('status');
            $table->index('make');
            $table->index('model');
            $table->index('year');
            $table->index('price_jpy');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
