<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kpi_grades', function (Blueprint $table) {
            if (!Schema::hasColumn('kpi_grades', 'career_path_requirements')) {
                $table->text('career_path_requirements')->nullable()->after('ekspektasi_kompetensi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_grades', function (Blueprint $table) {
            if (Schema::hasColumn('kpi_grades', 'career_path_requirements')) {
                $table->dropColumn('career_path_requirements');
            }
        });
    }
};
