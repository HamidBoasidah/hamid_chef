<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_service_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_service_id')->constrained('chef_services')->cascadeOnDelete();
            $table->string('name', 100);
            $table->boolean('is_included')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('chef_service_id', 'idx_chef_service_equipment_service_id');
            $table->index('is_included', 'idx_chef_service_equipment_included');
            
            // Unique constraint to prevent duplicate equipment names per service
            $table->unique(['chef_service_id', 'name'], 'unique_equipment_per_service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_service_equipment');
    }
};