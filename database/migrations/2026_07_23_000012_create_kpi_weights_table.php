<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_grade_id')->constrained('kpi_grades')->cascadeOnDelete();
            $table->foreignId('kpi_criteria_id')->constrained('kpi_criterias')->cascadeOnDelete();
            $table->decimal('weight_percent', 5, 2);
            $table->timestamps();

            $table->unique(['kpi_grade_id', 'kpi_criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_weights');
    }
};
