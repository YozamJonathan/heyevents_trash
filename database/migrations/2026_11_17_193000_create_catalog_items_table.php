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
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_profile_id')
                ->nullable()
                ->constrained('service_provider_profiles')
                ->nullOnDelete();
            $table->string('title');
            $table->string('category'); // rentals, sound, venues, catering, decor, photography, protocol, entertainment
            $table->text('description')->nullable();
            $table->decimal('client_price', 15, 2); // Price displayed to clients in TZS
            $table->decimal('provider_payout', 15, 2)->nullable(); // Payout paid to provider by HEY Events
            $table->string('pricing_unit')->default('per event'); // per piece, per day, per event, per plate, per hour
            $table->string('location')->default('Dar es Salaam');
            $table->string('image_url')->nullable();
            $table->integer('stock_quantity')->default(1);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
