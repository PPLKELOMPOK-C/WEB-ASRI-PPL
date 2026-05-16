@extends('layouts.public')
@section('title','Berita & Informasi - ASRI')

@section('content')
<div style="background:var(--green-900);padding:48px 5%;text-align:center">
    <h1 style="font-family:'Playfair Display',serif;font-size:34px;font-weight:700;color:white;margin-bottom:12px">Berita & Informasi</h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.75)">Informasi terbaru seputar kegiatan dan kebijakan rusun</p>
</div>

<div style="padding:48px 5%">
    <div class="container" style="padding:0">
        <form method="GET" style="display:flex;gap:10px;margin-bottom:32px">
            <input type="text" name="search" placeholder="Cari berita..." value="{{ request('search') }}" style="flex:1;padding:10px 14px;border:1.5px solid #e8f0eb;border-radius:8px;font-family:inherit;font-size:14px;outline:none;max-width:360px">
            <button type="submit" style="background:var(--green-700);color:white;border:none;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit">Cari</button>
        </form>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
        @forelse($news as $n)
        <a href="{{ route('public.news.detail', $n->slug) }}" style="text-decoration:none;background:white;border:1px solid #e8f0eb;border-radius:14px;overflow:hidden;transition:all 0.2s;display:block" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.09)'" onmouseout="this.style.transform='';this.style.boxShadow='none'">
            <div style="height:180px;background:var(--green-100);overflow:hidden;display:flex;align-items:center;justify-content:center">
                @if($n->gambar_cover)
                <img src="{{ Storage::url($n->gambar_cover) }}" style="width:100%;height:100%;object-fit:cover">
                @else
                <i class="ri-newspaper-line" style="font-size:44px;color:var(--green-400)"></i>
                @endif
            </div>
            <div style="padding:18px">
                <div style="font-size:11px;color:var(--green-500);font-weight:600;margin-bottom:8px">{{ $n->published_at->format('d M Y') }}</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--green-900);line-height:1.4;margin-bottom:8px">{{ Str::limit($n->judul, 70) }}</h3>
                <p style="font-size:13px;color:#5a7a5a;line-height:1.6">{{ Str::limit(strip_tags($n->konten), 100) }}</p>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:64px;color:#5a7a5a">
            <i class="ri-newspaper-line" style="font-size:48px;display:block;opacity:0.3;margin-bottom:12px"></i>
            <div style="font-size:16px;font-weight:600">Belum ada berita</div>
        </div>
        @endforelse
        </div>

        <div style="margin-top:32px">{{ $news->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
