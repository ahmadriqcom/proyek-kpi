<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->foreignId('kpi_category_id')->nullable()->constrained('kpi_categories')->nullOnDelete()->after('problem');
            $table->foreignId('kpi_priority_id')->nullable()->constrained('kpi_priorities')->nullOnDelete()->after('kpi_category_id');
            $table->foreignId('kpi_impact_level_id')->nullable()->constrained('kpi_impact_levels')->nullOnDelete()->after('kpi_priority_id');
            $table->text('approval_reason')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->dropForeign(['kpi_category_id']);
            $table->dropForeign(['kpi_priority_id']);
            $table->dropForeign(['kpi_impact_level_id']);
            $table->dropColumn(['kpi_category_id', 'kpi_priority_id', 'kpi_impact_level_id', 'approval_reason']);
        });
    }
};
