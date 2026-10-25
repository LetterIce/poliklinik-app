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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('tarif')->default(150000)->after('id_poli');
        });

        Schema::table('periksa', function (Blueprint $table) {
            $table->integer('biaya_dokter')->default(150000)->after('biaya_periksa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tarif');
        });

        Schema::table('periksa', function (Blueprint $table) {
            $table->dropColumn('biaya_dokter');
        });
    }
};
