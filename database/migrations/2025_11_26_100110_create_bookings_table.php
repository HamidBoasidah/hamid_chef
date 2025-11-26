<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('chef_id')->constrained('chefs')->cascadeOnDelete();
            $table->foreignId('chef_service_id')->constrained('chef_services')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->unsignedSmallInteger('hours_count')->default(1);
            $table->unsignedSmallInteger('number_of_guests')->default(1);
            $table->enum('service_type', ['hourly', 'package']);
            $table->decimal('unit_price', 10, 2);
            $table->unsignedSmallInteger('extra_guests_count')->default(0);
            $table->decimal('extra_guests_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->enum('booking_status', ['pending', 'accepted', 'rejected', 'cancelled_by_customer', 'cancelled_by_chef', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['chef_id', 'date']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
