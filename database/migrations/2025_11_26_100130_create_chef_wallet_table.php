<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_wallet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('chefs')->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('chef_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_wallet');
    }
};
