<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_formula_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('use_category_weight')->default(true);
            $table->boolean('use_priority_weight')->default(true);
            $table->boolean('use_impact_weight')->default(true);
            $table->boolean('use_sla_penalty')->default(true);
            $table->boolean('use_sla_bonus')->default(true);
            $table->decimal('sla_penalty_per_day', 5, 2)->default(10.00);
            $table->decimal('sla_bonus_early', 5, 2)->default(5.00);
            $table->boolean('cap_max_score')->default(true);
            $table->decimal('max_score_cap', 5, 2)->default(100.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_formula_configs');
    }
};
