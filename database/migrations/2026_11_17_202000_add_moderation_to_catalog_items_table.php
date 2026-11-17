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
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('is_featured'); // pending, approved, rejected
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('admin_notes');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'admin_notes', 'approved_at', 'approved_by']);
        });
    }
};
