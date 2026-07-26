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
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->string('category')->default('Technical/Bug')->after('problem');
            $table->string('priority')->default('Medium')->after('category');
            $table->string('impact_level')->default('Medium')->after('priority');
            $table->string('attachment_path')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_reports', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority', 'impact_level', 'attachment_path']);
        });
    }
};
