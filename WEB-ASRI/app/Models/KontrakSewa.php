<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KontrakSewa extends Model
{
    protected $fillable = ['user_id','unit_id','pengajuan_sewa_id','tanggal_mulai','tanggal_selesai','harga_per_bulan','status','dokumen_kontrak'];
    protected $casts = ['tanggal_mulai'=>'date','tanggal_selesai'=>'date','harga_per_bulan'=>'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function pengajuanSewa() { return $this->belongsTo(PengajuanSewa::class); }
}
