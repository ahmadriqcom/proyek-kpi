<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('score')->unique(); // 1 to 5
            $table->string('label', 50); // Sangat Kurang, Kurang, Cukup, Baik, Sangat Baik
            $table->decimal('converted_value', 5, 2); // 20, 40, 60, 80, 100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_levels');
    }
};
