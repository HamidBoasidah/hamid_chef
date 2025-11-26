<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('chefs')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->enum('service_type', ['hourly', 'package']);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->unsignedSmallInteger('min_hours')->nullable();
            $table->decimal('package_price', 10, 2)->nullable();
            $table->unsignedSmallInteger('max_guests_included')->nullable();
            $table->unsignedSmallInteger('max_guests')->nullable();
            $table->boolean('allow_extra_guests')->default(false);
            $table->decimal('extra_guest_price', 10, 2)->default(0);
            $table->unsignedSmallInteger('max_guests_allowed')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_services');
    }
};
