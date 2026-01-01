<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_vacations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chef_id');
            $table->date('date');
            $table->string('note', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chef_id')->references('id')->on('chefs')->onDelete('cascade');
            $table->unique(['chef_id', 'date'  , 'deleted_at']);
            $table->index(['chef_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_vacations');
    }
};
