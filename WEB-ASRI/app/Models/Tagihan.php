<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    // Sesuaikan fillable dengan nama kolom di database
    protected $fillable = [
        'user_id', 
        'unit_id', 
        'jumlah', 
        'periode', 
        'jatuh_tempo', // Jika di DB namanya jatuh_tempo
        'status', 
        'bukti_bayar', 
        'tanggal_bayar', 
        'catatan'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jatuh_tempo' => 'date', // Pastikan ini sesuai nama kolom di database
        'tanggal_bayar' => 'datetime'
    ];

    // Accessor: Agar di Blade bisa panggil $tagihan->tanggal_jatuh_tempo 
    // meskipun di DB namanya jatuh_tempo
    public function getTanggalJatuhTempoAttribute()
    {
        return $this->jatuh_tempo;
    }

    public function user() { return $this->belongsTo(User::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
}