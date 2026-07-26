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
        Schema::table('kpi_appraisals', function (Blueprint $table) {
            if (!Schema::hasColumn('kpi_appraisals', 'evaluator_justification')) {
                $table->text('evaluator_justification')->nullable()->after('approval_notes');
            }
            if (!Schema::hasColumn('kpi_appraisals', 'strongest_competency')) {
                $table->text('strongest_competency')->nullable()->after('evaluator_justification');
            }
            if (!Schema::hasColumn('kpi_appraisals', 'weakest_competency')) {
                $table->text('weakest_competency')->nullable()->after('strongest_competency');
            }
            if (!Schema::hasColumn('kpi_appraisals', 'executive_summary')) {
                $table->text('executive_summary')->nullable()->after('weakest_competency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_appraisals', function (Blueprint $table) {
            if (Schema::hasColumn('kpi_appraisals', 'evaluator_justification')) {
                $table->dropColumn('evaluator_justification');
            }
            if (Schema::hasColumn('kpi_appraisals', 'strongest_competency')) {
                $table->dropColumn('strongest_competency');
            }
            if (Schema::hasColumn('kpi_appraisals', 'weakest_competency')) {
                $table->dropColumn('weakest_competency');
            }
            if (Schema::hasColumn('kpi_appraisals', 'executive_summary')) {
                $table->dropColumn('executive_summary');
            }
        });
    }
};
