<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_sewa_id', 'user_id', 'jenis_dokumen',
        'nama_file', 'path_file', 'mime_type', 'ukuran_file', 'status',
    ];

    public function pengajuanSewa() { return $this->belongsTo(PengajuanSewa::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis_dokumen) {
            'ktp'               => 'KTP',
            'kk'                => 'Kartu Keluarga',
            'slip_gaji'         => 'Slip Gaji',
            'surat_keterangan'  => 'Surat Keterangan',
            default             => 'Lainnya',
        };
    }
}
