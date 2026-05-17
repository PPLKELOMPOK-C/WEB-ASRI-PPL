<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'unit_id', 
        'nik',           // WAJIB ADA
        'no_hp',         // WAJIB ADA
        'durasi_sewa', 
        'tanggal_mulai',
        'status', 
        'catatan_admin', 
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime', 
        'tanggal_mulai' => 'date'
    ];

    // Relasi
    public function user() { return $this->belongsTo(User::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function jadwalSurvei() { return $this->hasOne(JadwalSurvei::class); }
    public function kontrakSewa() { return $this->hasOne(KontrakSewa::class); }
    
    // Sesuaikan foreign key dokumens
    public function dokumens() { 
        return $this->hasMany(Dokumen::class, 'pengajuan_sewa_id'); 
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'              => 'Draft',
            'pending'            => 'Menunggu Verifikasi',
            'verifikasi_dokumen' => 'Verifikasi Dokumen',
            'jadwal_survei'      => 'Jadwal Survei',
            'diterima'           => 'Diterima',
            'ditolak'            => 'Ditolak',
            'dibatalkan'         => 'Dibatalkan',
            default              => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'diterima'   => 'success',
            'ditolak'    => 'danger',
            'dibatalkan' => 'secondary',
            'pending'    => 'warning',
            default      => 'info',
        };
    }
}