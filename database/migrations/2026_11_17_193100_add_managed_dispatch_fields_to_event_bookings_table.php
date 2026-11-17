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
        Schema::table('event_bookings', function (Blueprint $table) {
            $table->foreignId('catalog_item_id')
                ->nullable()
                ->after('user_id')
                ->constrained('catalog_items')
                ->nullOnDelete();

            $table->foreignId('assigned_provider_id')
                ->nullable()
                ->after('catalog_item_id')
                ->constrained('service_provider_profiles')
                ->nullOnDelete();

            $table->integer('item_quantity')->default(1)->after('assigned_provider_id');
            $table->string('client_phone')->nullable()->after('name');
            $table->string('venue_name')->nullable()->after('location');

            $table->decimal('total_client_amount', 15, 2)->nullable()->after('budget');
            $table->decimal('provider_payout_amount', 15, 2)->nullable()->after('total_client_amount');

            $table->string('dispatch_status')->default('pending_dispatch')->after('status');
            $table->string('payout_status')->default('unpaid')->after('dispatch_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_bookings', function (Blueprint $table) {
            $table->dropForeign(['catalog_item_id']);
            $table->dropForeign(['assigned_provider_id']);
            $table->dropColumn([
                'catalog_item_id',
                'assigned_provider_id',
                'item_quantity',
                'client_phone',
                'venue_name',
                'total_client_amount',
                'provider_payout_amount',
                'dispatch_status',
                'payout_status',
            ]);
        });
    }
};
