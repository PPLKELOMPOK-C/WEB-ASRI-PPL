@extends('layouts.public')
@section('title', $article->judul . ' - ASRI')

{{-- Push style Quill --}}
@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* Area Engine Konten Teks */
    .article-body-content {
        font-family: inherit;
        font-size: 15px;
        line-height: 1.9;
        color: #2d3d2d;
    }
    .article-body-content p {
        margin-bottom: 1.2rem;
    }
    /* Mengatasi link agar rapi dan tidak kepanjangan */
    .article-body-content a {
        color: var(--green-600, #3a7d4f);
        text-decoration: underline;
        font-weight: 500;
        word-break: break-word;
    }
    .article-body-content a:hover {
        color: var(--green-700, #2d6a3f);
    }
    /* Memunculkan kembali style bullet points & numbered lists dari Quill */
    .article-body-content ul, .article-body-content ol {
        padding-left: 24px;
        margin-bottom: 1.2rem;
    }
    .article-body-content li {
        margin-bottom: 6px;
    }
</style>
@endpush

@section('content')
<div style="padding:48px 5%">
    <div class="article-container">

        {{-- Bagian Artikel Utama --}}
        <div>
            <a href="{{ route('public.news') }}" style="font-size:13px;color:var(--green-600);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:20px">
                ← Kembali ke Berita
            </a>

            <div style="font-size:12px;color:var(--green-500);font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
                {{ $article->published_at->format('d M Y') }} · {{ $article->user->name }}
            </div>

            <h1 style="font-family:'Playfair Display',serif;font-size:34px;font-weight:700;color:var(--green-900);line-height:1.3;margin-bottom:24px">
                {{ $article->judul }}
            </h1>

            @if($article->gambar_cover)
            <img src="{{ Storage::url($article->gambar_cover) }}" style="width:100%;max-height:420px;object-fit:cover;border-radius:12px;margin-bottom:28px">
            @endif

            {{-- Render Konten HTML Secara Utuh & Aman --}}
            <div class="ql-editor article-body-content" style="padding:0">
                {!! $article->konten !!}
            </div>
        </div>

        {{-- Bagian Sidebar Kanan --}}
        <div class="article-sidebar">
            @if($related->count())
            <div style="background:white;border:1px solid #e8f0eb;border-radius:12px;padding:20px">
                <div style="font-size:14px;font-weight:700;color:var(--green-900);margin-bottom:14px">Berita Lainnya</div>
                @foreach($related as $r)
                <a href="{{ route('public.news.detail', $r->slug) }}" style="display:flex;gap:10px;text-decoration:none;padding:10px 0;border-bottom:1px solid #f0f5f1">
                    <div style="width:60px;height:50px;background:var(--green-100);border-radius:6px;flex-shrink:0;overflow:hidden">
                        @if($r->gambar_cover)
                        <img src="{{ Storage::url($r->gambar_cover) }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%">
                            <i class="ri-newspaper-line" style="color:var(--green-400)"></i>
                        </div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--green-900);line-height:1.3">
                            {{ Str::limit($r->judul, 50) }}
                        </div>
                        <div style="font-size:11px;color:#5a7a5a;margin-top:3px">
                            {{ $r->published_at->format('d M Y') }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

            <div style="margin-top:16px;background:var(--green-700);border-radius:12px;padding:20px;text-align:center">
                <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:white;margin-bottom:6px">Tertarik Tinggal?</div>
                <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-bottom:14px">Cari unit rusun tersedia di Jakarta</p>
                <a href="{{ route('public.units') }}" style="display:block;background:white;color:var(--green-800);padding:10px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">Lihat Unit →</a>
            </div>
        </div>

    </div>
</div>

{{-- Responsive Layout Engine Detail Berita --}}
<style>
.article-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 36px;
    align-items: start;
    max-width: 1200px;
    margin: 0 auto;
}
.article-sidebar {
    position: sticky;
    top: 80px;
}

@media(max-width: 900px) {
    .article-container {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .article-sidebar {
        position: static;
    }
}
</style>
@endsection