<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class News extends Model
{
    protected $fillable = ['user_id','judul','slug','konten','gambar_cover','is_published','published_at'];
    protected $casts = ['is_published'=>'boolean','published_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public static function generateSlug($judul): string
    {
        return Str::slug($judul) . '-' . time();
    }
    public function scopePublished($query) { return $query->where('is_published', true); }
}
