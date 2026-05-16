@extends('layouts.app')

@section('title', 'Edit Berita')
@section('page-title', 'Edit Berita: ' . $news->judul)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-title">
            <i class="ri-edit-box-line" style="color: var(--green-600);"></i> 
            Perbarui Informasi Berita
        </div>

        {{-- Menampilkan Error Validasi --}}
        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.berita.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; gap: 20px;">
                {{-- Judul Berita --}}
                <div class="form-group">
                    <label class="form-label">Judul Berita <span style="color:red">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                           value="{{ old('judul', $news->judul) }}" required>
                    @error('judul')
                        <small style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    {{-- Kategori (Jika ada kolom kategori di DB) --}}
                    <div class="form-group">
                        <label class="form-label">Kategori <span style="color:red">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="Pengumuman" @selected(old('kategori', $news->kategori) == 'Pengumuman')>Pengumuman</option>
                            <option value="Kegiatan" @selected(old('kategori', $news->kategori) == 'Kegiatan')>Kegiatan</option>
                            <option value="Info Penting" @selected(old('kategori', $news->kategori) == 'Info Penting')>Info Penting</option>
                            <option value="Promo" @selected(old('kategori', $news->kategori) == 'Promo')>Promo</option>
                        </select>
                    </div>

                    {{-- Status Publikasi --}}
                    <div class="form-group">
                        <label class="form-label">Status Publikasi <span style="color:red">*</span></label>
                        <select name="is_published" class="form-control" required>
                            <option value="1" @selected(old('is_published', $news->is_published) == 1)>Terbit (Published)</option>
                            <option value="0" @selected(old('is_published', $news->is_published) == 0)>Draft (Arsip)</option>
                        </select>
                    </div>
                </div>

                {{-- Gambar Cover --}}
                <div class="form-group">
                    <label class="form-label">Gambar Sampul</label>
                    <div style="border: 2px dashed var(--green-200); padding: 20px; border-radius: 10px; background: var(--green-50);">
                        
                        {{-- Preview Gambar Lama --}}
                        <div style="margin-bottom: 15px; text-align: center;">
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Gambar saat ini:</p>
                            @if($news->gambar_cover)
                                <img src="{{ asset('storage/' . $news->gambar_cover) }}" alt="Current Cover" 
                                     style="max-width: 200px; border-radius: 8px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @else
                                <p style="color: #999; font-style: italic;">Tidak ada gambar</p>
                            @endif
                        </div>

                        <input type="file" name="gambar_cover" id="gambarBerita" class="form-control" accept="image/*" 
                               onchange="previewImage(this)">
                        <small style="display: block; color: #666; margin-top: 5px;">Biarkan kosong jika tidak ingin mengubah gambar (Max: 3MB)</small>

                        {{-- Preview Gambar Baru --}}
                        <div id="imagePreview" style="margin-top: 15px; display: none; text-align: center;">
                            <p style="font-size: 12px; color: var(--green-700); font-weight: bold; margin-bottom: 8px;">Preview gambar baru:</p>
                            <img id="preview" src="#" alt="New Preview" style="max-width: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                </div>

                {{-- Isi Konten --}}
                <div class="form-group">
                    <label class="form-label">Konten Berita <span style="color:red">*</span></label>
                    <textarea name="konten" id="konten" class="form-control @error('konten') is-invalid @enderror" 
                              rows="12" required style="resize: vertical;">{{ old('konten', $news->konten) }}</textarea>
                    @error('konten')
                        <small style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary" style="padding: 12px 25px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 35px; background: var(--green-700);">
                    <i class="ri-save-line"></i> Perbarui Berita
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const container = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection