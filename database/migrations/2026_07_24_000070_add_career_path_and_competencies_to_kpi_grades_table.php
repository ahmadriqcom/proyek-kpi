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
            if (!Schema::hasColumn('kpi_grades', 'career_path')) {
                $table->string('career_path', 150)->nullable()->after('nama_grade');
            }
            if (!Schema::hasColumn('kpi_grades', 'deskripsi_kompetensi')) {
                $table->text('deskripsi_kompetensi')->nullable()->after('career_path');
            }
            if (!Schema::hasColumn('kpi_grades', 'tujuan_grade')) {
                $table->text('tujuan_grade')->nullable()->after('deskripsi_kompetensi');
            }
            if (!Schema::hasColumn('kpi_grades', 'ekspektasi_kompetensi')) {
                $table->text('ekspektasi_kompetensi')->nullable()->after('tujuan_grade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_grades', function (Blueprint $table) {
            if (Schema::hasColumn('kpi_grades', 'career_path')) {
                $table->dropColumn('career_path');
            }
            if (Schema::hasColumn('kpi_grades', 'deskripsi_kompetensi')) {
                $table->dropColumn('deskripsi_kompetensi');
            }
            if (Schema::hasColumn('kpi_grades', 'tujuan_grade')) {
                $table->dropColumn('tujuan_grade');
            }
            if (Schema::hasColumn('kpi_grades', 'ekspektasi_kompetensi')) {
                $table->dropColumn('ekspektasi_kompetensi');
            }
        });
    }
};
