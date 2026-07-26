<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_appraisal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_appraisal_id')->constrained('kpi_appraisals')->cascadeOnDelete();
            $table->foreignId('kpi_criteria_id')->constrained('kpi_criterias');
            $table->decimal('weight_percent', 5, 2);
            $table->unsignedTinyInteger('score_input'); // 1 to 5
            $table->decimal('converted_value', 5, 2); // 20 to 100
            $table->decimal('weighted_score', 5, 2); // (weight_percent * converted_value) / 100
            $table->text('indicator_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_appraisal_details');
    }
};
