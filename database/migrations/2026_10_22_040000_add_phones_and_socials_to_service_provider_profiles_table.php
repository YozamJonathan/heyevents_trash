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
        Schema::table('service_provider_profiles', function (Blueprint $table) {
            $table->string('primary_phone', 50)->nullable()->after('nida_number');
            $table->string('secondary_phone', 50)->nullable()->after('primary_phone');
            $table->json('categories')->nullable()->after('category');
            $table->string('instagram')->nullable()->after('social_media');
            $table->string('whatsapp')->nullable()->after('instagram');
            $table->string('tiktok')->nullable()->after('whatsapp');
            $table->string('social_media')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_provider_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'primary_phone',
                'secondary_phone',
                'categories',
                'instagram',
                'whatsapp',
                'tiktok',
            ]);
        });
    }
};
