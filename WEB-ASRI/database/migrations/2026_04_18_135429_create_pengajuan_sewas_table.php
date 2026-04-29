<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_sewas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->integer('durasi_sewa'); // dalam bulan, maks 72 bulan (6 tahun)
            $table->date('tanggal_mulai')->nullable();
            $table->enum('status', [
                'draft', 'pending', 'verifikasi_dokumen',
                'jadwal_survei', 'diterima', 'ditolak', 'dibatalkan'
            ])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_sewas');
    }
};
