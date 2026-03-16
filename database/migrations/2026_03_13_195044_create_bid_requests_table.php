<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bid_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Vehicle reference (local or external)
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_vehicle_id')->nullable();
            $table->string('vehicle_source'); // 'company' | 'third_party' | 'auction'

            // Bid details
            $table->unsignedBigInteger('max_bid_amount')->nullable(); // in JPY
            $table->text('notes')->nullable();

            // Status tracking
            $table->string('status')->default('pending'); // 'pending' | 'processing' | 'won' | 'lost' | 'cancelled'

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bid_requests');
    }
};
