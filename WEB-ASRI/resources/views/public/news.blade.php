@extends('layouts.public')
@section('title','Berita & Informasi - ASRI')

@section('content')

{{-- Hero --}}
<div style="background:var(--green-900);padding:48px 5%;text-align:center">
    <h1 style="font-family:'Playfair Display',serif;font-size:34px;font-weight:700;color:white;margin-bottom:12px">
        Berita & Informasi
    </h1>
    <p style="font-size:15px;color:rgba(255,255,255,0.75)">
        Informasi terbaru seputar kegiatan dan kebijakan rusun
    </p>
</div>

<div style="padding:48px 5%;background:#f7fbf8;min-height:60vh">
    <div class="container" style="padding:0">

        {{-- Filter Bar --}}
        <form method="GET" style="background:white;border:1px solid #e8f0eb;border-radius:14px;padding:20px 24px;margin-bottom:32px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">

            {{-- Search --}}
            <div style="flex:1;min-width:200px">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--green-700);margin-bottom:6px">Cari Berita</label>
                <div style="display:flex;gap:0">
                    <input type="text" name="search"
                        placeholder="Judul atau tanggal (mis: 12-05-2025)..."
                        value="{{ request('search') }}"
                        style="flex:1;padding:10px 14px;border:1.5px solid #e8f0eb;border-radius:8px 0 0 8px;font-family:inherit;font-size:14px;outline:none;color:var(--green-900)">
                    <button type="submit"
                        style="background:var(--green-700);color:white;border:none;padding:10px 18px;border-radius:0 8px 8px 0;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </div>

            {{-- Kategori --}}
            <div style="min-width:160px">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--green-700);margin-bottom:6px">Kategori</label>
                <select name="kategori" onchange="this.form.submit()"
                    style="width:100%;padding:10px 14px;border:1.5px solid #e8f0eb;border-radius:8px;font-family:inherit;font-size:14px;color:var(--green-900);outline:none;background:white;cursor:pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $key => $label)
                    <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort --}}
            <div style="min-width:160px">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--green-700);margin-bottom:6px">Urutkan</label>
                <select name="sort" onchange="this.form.submit()"
                    style="width:100%;padding:10px 14px;border:1.5px solid #e8f0eb;border-radius:8px;font-family:inherit;font-size:14px;color:var(--green-900);outline:none;background:white;cursor:pointer">
                    <option value="terbaru" {{ request('sort','terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            {{-- Reset --}}
            @if(request()->hasAny(['search','kategori','sort']))
            <div style="min-width:fit-content;padding-bottom:1px">
                <label style="display:block;font-size:12px;margin-bottom:6px;opacity:0">&nbsp;</label>
                <a href="{{ route('public.news') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border:1.5px solid #e8f0eb;border-radius:8px;font-size:13px;font-weight:600;color:#5a7a5a;text-decoration:none;background:white;white-space:nowrap">
                    <i class="ri-refresh-line"></i> Reset
                </a>
            </div>
            @endif
        </form>

        {{-- Active Filters Info --}}
        @if(request()->hasAny(['search','kategori','sort']))
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px">
            <span style="font-size:13px;color:#5a7a5a">Filter aktif:</span>
            @if(request('search'))
            <span style="background:var(--green-100);color:var(--green-700);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600">
                <i class="ri-search-line"></i> "{{ request('search') }}"
            </span>
            @endif
            @if(request('kategori') && isset($kategoriList[request('kategori')]))
            <span style="background:var(--green-100);color:var(--green-700);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600">
                <i class="ri-price-tag-3-line"></i> {{ $kategoriList[request('kategori')] }}
            </span>
            @endif
            @if(request('sort'))
            <span style="background:var(--green-100);color:var(--green-700);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600">
                <i class="ri-sort-desc"></i> {{ request('sort') === 'terlama' ? 'Terlama' : 'Terbaru' }}
            </span>
            @endif
            <span style="font-size:13px;color:#5a7a5a;margin-left:4px">
                — {{ $news->total() }} berita ditemukan
            </span>
        </div>
        @endif

        {{-- Grid Berita --}}
        <div class="news-grid">
        @forelse($news as $n)

            {{-- Badge warna per kategori --}}
            @php
                $badgeColor = match($n->kategori) {
                    'pengumuman'  => ['bg'=>'#fff3cd','text'=>'#856404'],
                    'kegiatan'    => ['bg'=>'#d1e7dd','text'=>'#0a5c36'],
                    'info_penting'=> ['bg'=>'#f8d7da','text'=>'#842029'],
                    'promo'       => ['bg'=>'#cfe2ff','text'=>'#084298'],
                    default       => ['bg'=>'var(--green-100)','text'=>'var(--green-700)'],
                };
            @endphp

            <a href="{{ route('public.news.detail', $n->slug) }}" class="news-card">

                {{-- Cover --}}
                <div style="height:180px;background:var(--green-100);overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative">
                    @if($n->gambar_cover)
                    <img src="{{ Storage::url($n->gambar_cover) }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <i class="ri-newspaper-line" style="font-size:44px;color:var(--green-400)"></i>
                    @endif

                    {{-- Kategori badge di atas gambar --}}
                    <span style="position:absolute;top:10px;left:10px;background:{{ $badgeColor['bg'] }};color:{{ $badgeColor['text'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.3px">
                        {{ $n->label_kategori }}
                    </span>
                </div>

                <div style="padding:18px;flex:1;display:flex;flex-direction:column">
                    <div style="font-size:11px;color:var(--green-500);font-weight:600;margin-bottom:8px">
                        <i class="ri-calendar-line" style="margin-right:3px"></i>
                        {{ $n->published_at->format('d M Y') }}
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:var(--green-900);line-height:1.4;margin-bottom:8px;flex:1">
                        {{ Str::limit($n->judul, 70) }}
                    </h3>
                    {{-- Bersihkan tag HTML untuk ringkasan teks deskripsi --}}
                    <p style="font-size:13px;color:#5a7a5a;line-height:1.6;margin-bottom:0">
                        {{ Str::limit(strip_tags($n->konten), 100) }}
                    </p>
                </div>
            </a>

        @empty
            <div style="grid-column:1/-1;text-align:center;padding:80px 32px;color:#5a7a5a">
                <i class="ri-newspaper-line" style="font-size:56px;display:block;opacity:0.25;margin-bottom:16px"></i>
                <div style="font-size:17px;font-weight:700;color:var(--green-900);margin-bottom:6px">Berita tidak ditemukan</div>
                <div style="font-size:14px">Coba ubah kata kunci atau reset filter pencarian</div>
            </div>
        @endforelse
        </div>

        {{-- Pagination --}}
        <div style="margin-top:40px">{{ $news->withQueryString()->links() }}</div>
    </div>
</div>

{{-- Responsive Layout Styles --}}
<style>
.news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.news-card {
    text-decoration: none;
    background: white;
    border: 1px solid #e8f0eb;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}
.news-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.09);
}

@media(max-width:900px){
    .news-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media(max-width:560px){
    .news-grid {
        grid-template-columns: 1fr;
    }
    form[method="GET"]{
        flex-direction: column !important;
        align-items: stretch !important;
    }
}
</style>
@endsection