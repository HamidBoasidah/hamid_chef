<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kycs', 'is_active')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('kycs', 'is_active')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('document_scan_copy');
            });
        }
    }
};
