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
        Schema::table('bookings', function (Blueprint $table) {
            // Composite index for conflict detection queries
            $table->index(['chef_id', 'booking_status', 'is_active'], 'bookings_chef_status_active_idx');
            
            // Index for date range queries
            $table->index(['date', 'start_time'], 'bookings_date_time_idx');
            
            // Index for time-based conflict detection
            $table->index(['chef_id', 'date', 'start_time'], 'bookings_chef_date_time_idx');
            
            // Index for status and date filtering
            $table->index(['booking_status', 'date'], 'bookings_status_date_idx');
            
            // Index for payment status queries
            $table->index(['payment_status', 'created_at'], 'bookings_payment_created_idx');
            
            // Index for admin filtering and sorting
            $table->index(['is_active', 'created_at'], 'bookings_active_created_idx');
            
            // Index for customer bookings
            $table->index(['customer_id', 'booking_status', 'date'], 'bookings_customer_status_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_chef_status_active_idx');
            $table->dropIndex('bookings_date_time_idx');
            $table->dropIndex('bookings_chef_date_time_idx');
            $table->dropIndex('bookings_status_date_idx');
            $table->dropIndex('bookings_payment_created_idx');
            $table->dropIndex('bookings_active_created_idx');
            $table->dropIndex('bookings_customer_status_date_idx');
        });
    }
};
