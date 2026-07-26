<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_criterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kriteria', 50)->unique();
            $table->string('nama_kriteria', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('bobot_default', 5, 2)->default(10.00);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_criterias');
    }
};
