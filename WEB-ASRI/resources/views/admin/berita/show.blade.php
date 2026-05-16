@extends('layouts.app')

@section('title', 'Detail Berita')
@section('page-title', 'Preview Berita')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    
    {{-- Tombol Navigasi --}}
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.berita.edit', $news->id) }}" class="btn btn-primary" style="background: var(--green-600);">
                <i class="ri-edit-line"></i> Edit Berita
            </a>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        {{-- Header Gambar --}}
        <div style="width: 100%; height: 400px; overflow: hidden; position: relative; background: #eee;">
            {{-- Sesuaikan nama kolom dengan controller: gambar_cover --}}
            @if($news->gambar_cover)
                <img src="{{ asset('storage/' . $news->gambar_cover) }}" alt="{{ $news->judul }}" 
                     style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #999;">
                    <i class="ri-image-line" style="font-size: 50px;"></i>
                    <span>Tidak ada gambar sampul</span>
                </div>
            @endif
            
            <div style="position: absolute; bottom: 20px; left: 20px;">
                <span class="badge" style="background: var(--green-600); color: white; padding: 8px 15px; font-size: 14px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                    {{-- Ganti jika ada kolom kategori, jika tidak gunakan teks statis dulu --}}
                    Berita ASRI
                </span>
            </div>
        </div>

        {{-- Konten Berita --}}
        <div style="padding: 40px;">
            <div style="margin-bottom: 30px;">
                <h1 style="color: var(--green-900); font-size: 32px; margin-bottom: 15px; line-height: 1.2;">
                    {{ $news->judul }}
                </h1>
                
                <div style="display: flex; gap: 20px; align-items: center; color: #666; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ri-user-3-line"></i> 
                        <strong>{{ $news->user->name ?? 'Admin' }}</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ri-calendar-event-line"></i>
                        {{ $news->created_at->format('d M Y, H:i') }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ri-eye-line"></i>
                        Status: 
                        {{-- Sesuaikan dengan kolom is_published di controller --}}
                        <span style="font-weight: bold; color: {{ $news->is_published ? 'var(--green-600)' : '#f59e0b' }}">
                            {{ $news->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Isi Berita --}}
            <div style="line-height: 1.8; color: #333; font-size: 16px; white-space: pre-line;">
                {!! nl2br(e($news->konten)) !!}
            </div>
        </div>
    </div>
</div>
@endsection