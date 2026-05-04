<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Notifikasi extends Model
{
    protected $fillable = ['user_id','judul','pesan','tipe','link','is_read'];
    protected $casts = ['is_read' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
