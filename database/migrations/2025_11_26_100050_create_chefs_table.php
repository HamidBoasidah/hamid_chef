<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chefs', function (Blueprint $table) {
            $table->id();
            // link to users table - use user_id as the referencing column
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('governorate_id')->constrained('governorates')->onDelete('cascade');
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->decimal('base_hourly_rate', 10, 2)->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chefs');
    }
};
