<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Tambahin ini kalau relasi dipakai
use App\Models\PengajuanSewa;
use App\Models\Tagihan;
use App\Models\LaporanKerusakan;
use App\Models\Notifikasi;
use App\Models\KontrakSewa;
use App\Models\Dokumen;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'nik',
        'foto_profil',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCalonPenghuni(): bool
    {
        return $this->role === 'calon_penghuni';
    }

    public function isPenghuni(): bool
    {
        return $this->role === 'penghuni';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function pengajuanSewas()
    {
        return $this->hasMany(PengajuanSewa::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function laporanKerusakans()
    {
        return $this->hasMany(LaporanKerusakan::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function kontrakSewas()
    {
        return $this->hasMany(KontrakSewa::class);
    }

    public function kontrakAktif()
    {
        return $this->hasOne(KontrakSewa::class)
                    ->where('status', 'aktif');
    }

    public function unreadNotifications()
    {
        return $this->notifikasis()
                    ->where('is_read', false);
    }

    /**
     * Relasi ke dokumen melalui pengajuan sewa
     */
    public function dokumens()
{
    return $this->hasManyThrough(
        \App\Models\Dokumen::class, 
        \App\Models\PengajuanSewa::class, 
        'user_id',           // Foreign key di pengajuan_sewas
        'pengajuan_sewa_id', // Foreign key di dokumens (Harus sesuai nama kolom di DB)
        'id',                // Local key di users
        'id'                 // Local key di pengajuan_sewas
    );
}
}