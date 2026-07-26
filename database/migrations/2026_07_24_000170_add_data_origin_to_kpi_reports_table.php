<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->boolean('is_auto_interpreted')->default(true)->after('kpi_impact_level_id');
            $table->string('data_origin', 50)->default('AUTO_INTERPRETED')->after('is_auto_interpreted');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->dropColumn(['is_auto_interpreted', 'data_origin']);
        });
    }
};
