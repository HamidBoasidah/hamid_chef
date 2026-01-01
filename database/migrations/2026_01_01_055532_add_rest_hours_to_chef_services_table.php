<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * إضافة حقل ساعات الراحة المطلوبة بعد كل حجز لكل خدمة
     */
    public function up(): void
    {
        Schema::table('chef_services', function (Blueprint $table) {
            // ساعات الراحة المطلوبة بعد انتهاء الحجز (افتراضي 2 ساعات)
            $table->unsignedTinyInteger('rest_hours_required')
                  ->default(2)
                  ->after('extra_guest_price')
                  ->comment('Required rest hours after booking ends');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chef_services', function (Blueprint $table) {
            $table->dropColumn('rest_hours_required');
        });
    }
};
