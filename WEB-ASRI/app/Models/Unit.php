<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'blok', 'lantai', 'no_kamar', 'gedung', 'alamat',
        'deskripsi', 'gambar', 'harga_sewa', 'status', 'wilayah', 'luas_m2',
    ];

    protected $casts = [
        'harga_sewa' => 'decimal:2',
        'luas_m2' => 'integer',
    ];

    public function pengajuanSewas() { return $this->hasMany(PengajuanSewa::class); }
    public function kontrakSewas() { return $this->hasMany(KontrakSewa::class); }
    public function tagihans() { return $this->hasMany(Tagihan::class); }
    public function laporanKerusakans() { return $this->hasMany(LaporanKerusakan::class); }

    public function penghuni()
    {
        return $this->hasOneThrough(User::class, KontrakSewa::class,
            'unit_id', 'id', 'id', 'user_id');
    }

    public function scopeTersedia($query) { return $query->where('status', 'tersedia'); }
    public function scopeWilayah($query, $wilayah) { return $query->where('wilayah', $wilayah); }

    public function getNamaUnitAttribute(): string
    {
        return "Blok {$this->blok} - Lt.{$this->lantai} No.{$this->no_kamar}";
    }
}
