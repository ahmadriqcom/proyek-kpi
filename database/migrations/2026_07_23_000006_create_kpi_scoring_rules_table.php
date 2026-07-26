<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('grade_level')->unique(); // Grade 1 to 6
            $table->string('grade_name');
            $table->integer('target_sla_days');
            $table->decimal('base_score', 8, 2)->default(100.00);
            $table->decimal('sla_penalty_per_day', 8, 2)->default(5.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_scoring_rules');
    }
};
