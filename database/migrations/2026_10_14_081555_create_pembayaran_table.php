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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periksa')->constrained('periksa')->cascadeOnDelete();
            $table->foreignId('id_pasien')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah_bayar');
            $table->string('metode_pembayaran')->default('Transfer Bank');
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'lunas', 'ditolak'])->default('menunggu_verifikasi');
            $table->dateTime('tgl_bayar');
            $table->dateTime('tgl_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
