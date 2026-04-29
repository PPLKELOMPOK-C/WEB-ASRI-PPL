<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('blok', 10);
            $table->integer('lantai');
            $table->string('no_kamar', 10);
            $table->string('gedung');
            $table->string('alamat');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->decimal('harga_sewa', 12, 2);
            $table->enum('status', ['tersedia', 'dihuni', 'maintenance'])->default('tersedia');
            $table->enum('wilayah', [
                'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Timur',
                'Jakarta Selatan', 'Jakarta Barat'
            ]);
            $table->integer('luas_m2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};