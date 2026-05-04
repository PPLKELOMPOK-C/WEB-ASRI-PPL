<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JadwalSurvei extends Model
{
    protected $fillable = ['pengajuan_sewa_id','user_id','tanggal_survei','status','catatan'];
    protected $casts = ['tanggal_survei' => 'datetime'];
    public function pengajuanSewa() { return $this->belongsTo(PengajuanSewa::class); }
    public function user() { return $this->belongsTo(User::class); }
}
