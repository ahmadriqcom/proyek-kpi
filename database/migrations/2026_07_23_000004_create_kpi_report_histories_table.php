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
        Schema::create('kpi_report_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_report_id')->constrained('kpi_reports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('previous_status', 50);
            $table->string('new_status', 50);
            $table->text('solution_log')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_report_histories');
    }
};
