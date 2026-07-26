<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_grade_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_grade_id')->constrained('kpi_grades')->cascadeOnDelete();
            $table->foreignId('kpi_criteria_id')->constrained('kpi_criterias')->cascadeOnDelete();
            $table->unsignedTinyInteger('score'); // 1 to 5
            $table->text('indicator_description');
            $table->timestamps();

            $table->unique(['kpi_grade_id', 'kpi_criteria_id', 'score'], 'grade_criteria_score_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_grade_schemes');
    }
};
