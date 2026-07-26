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
        Schema::table('regions', function (Blueprint $table) {
            if (!Schema::hasColumn('regions', 'province')) {
                $table->string('province', 150)->nullable()->after('name');
            }
            if (!Schema::hasColumn('regions', 'uraian_provinsi')) {
                $table->text('uraian_provinsi')->nullable()->after('province');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('regions', 'uraian_provinsi')) {
                $table->dropColumn('uraian_provinsi');
            }
        });
    }
};
