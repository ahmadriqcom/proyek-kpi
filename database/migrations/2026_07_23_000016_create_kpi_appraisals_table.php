<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_appraisals', function (Blueprint $table) {
            $table->id();
            $table->string('appraisal_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kpi_grade_id')->constrained('kpi_grades');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('total_score', 5, 2)->default(0.00);
            $table->string('predicate', 50)->nullable();
            $table->text('recommendation')->nullable();
            $table->string('approval_status', 50)->default('draft'); // draft, submitted, approved, rejected
            $table->text('approval_notes')->nullable();
            $table->unsignedInteger('scheme_version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_appraisals');
    }
};
