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
        Schema::table('users', function (Blueprint $blueprint) {
            // Menambahkan kolom avatar setelah kolom email
            // nullable() digunakan agar user lama tidak wajib punya foto saat migrasi dijalankan
            $blueprint->string('avatar')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            // Menghapus kolom avatar jika migrasi di-rollback
            $blueprint->dropColumn('avatar');
        });
    }
};