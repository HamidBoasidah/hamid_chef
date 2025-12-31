<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chef_id');
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday .. 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chef_id')->references('id')->on('chefs')->onDelete('cascade');
            $table->index(['chef_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_working_hours');
    }
};
