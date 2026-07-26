<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_grades', function (Blueprint $table) {
            $table->id();
            $table->string('kode_grade', 50)->unique();
            $table->string('nama_grade', 150);
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedInteger('urutan_grade')->default(1);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_grades');
    }
};
