@extends('layouts.app')

@section('title', 'Buat Berita Baru')
@section('page-title', 'Tambah Konten Berita')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-title">
            <i class="ri-newspaper-line" style="color: var(--green-600);"></i> 
            Detail Informasi Berita
        </div>

        {{-- Menampilkan Error Validasi jika ada --}}
        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; gap: 20px;">
                {{-- Judul Berita --}}
                <div class="form-group">
                    <label class="form-label">Judul Berita <span style="color:red">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                           value="{{ old('judul') }}" placeholder="Contoh: Jadwal Pemeliharaan Fasilitas Bulan Mei" required>
                    @error('judul')
                        <small style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    {{-- Kategori (Jika di DB tidak ada kolom kategori, simpan sebagai bagian dari konten atau sesuaikan model) --}}
                    <div class="form-group">
                        <label class="form-label">Kategori <span style="color:red">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Pengumuman" @selected(old('kategori') == 'Pengumuman')>Pengumuman</option>
                            <option value="Kegiatan" @selected(old('kategori') == 'Kegiatan')>Kegiatan</option>
                            <option value="Info Penting" @selected(old('kategori') == 'Info Penting')>Info Penting</option>
                            <option value="Promo" @selected(old('kategori') == 'Promo')>Promo</option>
                        </select>
                    </div>

                    {{-- Status Publish (Sesuai logic Controller $request->boolean('is_published')) --}}
                    <div class="form-group">
                        <label class="form-label">Status Publikasi <span style="color:red">*</span></label>
                        <select name="is_published" class="form-control" required>
                            <option value="1" @selected(old('is_published') == '1')>Publikasikan Sekarang</option>
                            <option value="0" @selected(old('is_published') == '0')>Simpan sebagai Draft</option>
                        </select>
                    </div>
                </div>

                {{-- Gambar Cover - PERBAIKAN: name="gambar_cover" agar sesuai Controller --}}
                <div class="form-group">
                    <label class="form-label">Gambar Sampul <span style="color:red">*</span></label>
                    <div style="border: 2px dashed var(--green-200); padding: 20px; border-radius: 10px; text-align: center; background: var(--green-50);">
                        <input type="file" name="gambar_cover" id="gambarBerita" class="form-control @error('gambar_cover') is-invalid @enderror" 
                               accept="image/*" required onchange="previewImage(this)" style="margin-bottom: 10px;">
                        
                        <small style="display: block; color: #666;">Rekomendasi ukuran 1200x600px (Max: 3MB sesuai Controller)</small>
                        
                        @error('gambar_cover')
                            <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror

                        <div id="imagePreview" style="margin-top: 15px; display: none;">
                            <img id="preview" src="#" alt="Preview" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                </div>

                {{-- Isi Konten --}}
                <div class="form-group">
                    <label class="form-label">Konten Berita <span style="color:red">*</span></label>
                    <textarea name="konten" id="konten" class="form-control @error('konten') is-invalid @enderror" 
                              rows="12" placeholder="Tuliskan detail isi berita di sini..." required style="resize: vertical;">{{ old('konten') }}</textarea>
                    @error('konten')
                        <small style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary" style="padding: 12px 25px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 35px; background: var(--green-700);">
                    <i class="ri-save-line"></i> Simpan Berita
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