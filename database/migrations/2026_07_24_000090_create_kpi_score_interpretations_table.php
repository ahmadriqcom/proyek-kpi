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
        Schema::create('kpi_score_interpretations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_grade_id')->constrained('kpi_grades')->onDelete('cascade');
            $table->foreignId('kpi_criteria_id')->constrained('kpi_criterias')->onDelete('cascade');
            $table->integer('score'); // 1 s.d. 5
            $table->text('narasi_interpretasi');
            $table->text('area_pengembangan')->nullable();
            $table->text('rekomendasi_otomatis')->nullable();
            $table->timestamps();

            $table->unique(['kpi_grade_id', 'kpi_criteria_id', 'score'], 'idx_grade_crit_score_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_score_interpretations');
    }
};
