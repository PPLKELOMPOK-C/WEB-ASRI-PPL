@extends('layouts.app')
@section('title', 'Manajemen Berita')
@section('page-title', 'Berita & Pengumuman')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.berita.create') }}" class="btn btn-primary">
            <i class="ri-edit-box-line"></i> Tulis Berita Baru
        </a>

        {{-- Form Filter --}}
        <form action="{{ route('admin.berita.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" placeholder="Cari judul berita..." value="{{ request('search') }}" style="width: 250px;">
            
            <select name="status" class="form-control" style="width: 150px;">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <button type="submit" class="btn btn-secondary"><i class="ri-filter-3-line"></i> Filter</button>
        </form>
    </div>
</div>

<div class="grid grid-3">
    @forelse($news as $item)
    <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; position: relative;">
        {{-- Label Status --}}
        <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
            <span class="badge {{ $item->is_published ? 'badge-success' : 'badge-secondary' }}" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                {{ $item->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>

        {{-- Gambar Cover --}}
        <div style="width: 100%; height: 180px; background: #eee; overflow: hidden;">
            @if($item->gambar_cover)
                <img src="{{ Storage::url($item->gambar_cover) }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #aaa; background: #f9f9f9;">
                    <i class="ri-image-2-line" style="font-size: 48px;"></i>
                </div>
            @endif
        </div>

        {{-- Konten Card --}}
        <div style="padding: 16px; flex-grow: 1;">
            <div style="font-size: 11px; color: #888; margin-bottom: 6px; display: flex; align-items: center; gap: 5px;">
                <i class="ri-calendar-event-line"></i> {{ $item->created_at->format('d M Y') }}
                <span style="margin: 0 4px;">•</span>
                <i class="ri-user-smile-line"></i> {{ $item->user->name ?? 'Admin' }}
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--green-900); margin-bottom: 10px; line-height: 1.4;">
                {{ Str::limit($item->judul, 60) }}
            </h3>
            <p style="font-size: 13px; color: #555; line-height: 1.5; margin-bottom: 16px;">
                {{ Str::limit(strip_tags($item->konten), 100) }}
            </p>
        </div>

        {{-- Footer Card (Aksi) --}}
        <div style="padding: 12px 16px; background: #f8faf9; border-top: 1px solid #edf2ef; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn btn-sm btn-light" style="color: blue;" title="Edit"><i class="ri-pencil-line"></i></a>
                
                <form action="{{ route('admin.berita.toggle-publish', $item->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light" style="color: {{ $item->is_published ? 'orange' : 'green' }};" title="{{ $item->is_published ? 'Unpublish' : 'Publish' }}">
                        <i class="{{ $item->is_published ? 'ri-eye-off-line' : 'ri-eye-line' }}"></i>
                    </button>
                </form>

                <form action="{{ route('admin.berita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')" style="display: inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light" style="color: red;" title="Hapus"><i class="ri-delete-bin-line"></i></button>
                </form>
            </div>
            <a href="{{ route('admin.berita.show', $item->id) }}" style="font-size: 12px; font-weight: 600; color: var(--green-700); text-decoration: none;">Detail →</a>
        </div>
    </div>
    @empty
    <div style="grid-column: span 3; text-align: center; padding: 60px; background: white; border-radius: 12px; border: 2px dashed #e0e7e1;">
        <i class="ri-news-line" style="font-size: 48px; color: #ccc;"></i>
        <p style="margin-top: 12px; color: #888;">Belum ada berita yang ditulis.</p>
        <a href="{{ route('admin.berita.create') }}" class="btn btn-primary btn-sm" style="margin-top: 8px;">Buat Berita Pertama</a>
    </div>
    @endforelse
</div>

<div style="margin-top: 24px;">
    {{ $news->links() }}
</div>
@endsection