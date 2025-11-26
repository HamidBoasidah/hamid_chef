<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('chefs')->cascadeOnDelete();
            $table->foreignId('cuisine_id')->constrained('categories')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['chef_id', 'cuisine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_categories');
    }
};
