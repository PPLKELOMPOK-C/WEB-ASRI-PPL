<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_sewa_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_dokumen', ['ktp', 'kk', 'slip_gaji', 'surat_keterangan', 'lainnya']);
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('mime_type', 50);
            $table->integer('ukuran_file'); // dalam KB
            $table->enum('status', ['uploaded', 'verified', 'rejected'])->default('uploaded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
