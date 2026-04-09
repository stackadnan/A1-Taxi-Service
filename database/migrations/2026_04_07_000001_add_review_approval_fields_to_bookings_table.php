<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedTinyInteger('review_status')->nullable()->after('status_id');
            $table->timestamp('review_requested_at')->nullable()->after('review_status');
            $table->timestamp('review_approved_at')->nullable()->after('review_requested_at');
            $table->timestamp('review_rejected_at')->nullable()->after('review_approved_at');
            $table->timestamp('review_email_sent_at')->nullable()->after('review_rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'review_status',
                'review_requested_at',
                'review_approved_at',
                'review_rejected_at',
                'review_email_sent_at',
            ]);
        });
    }
};