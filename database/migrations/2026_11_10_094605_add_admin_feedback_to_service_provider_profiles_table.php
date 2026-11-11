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
            $table->text('admin_feedback')->nullable()->after('rejection_reason');
            $table->timestamp('feedback_sent_at')->nullable()->after('admin_feedback');
            $table->foreignId('feedback_sent_by')->nullable()->after('feedback_sent_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_provider_profiles', function (Blueprint $table) {
            $table->dropForeign(['feedback_sent_by']);
            $table->dropColumn(['admin_feedback', 'feedback_sent_at', 'feedback_sent_by']);
        });
    }
};
