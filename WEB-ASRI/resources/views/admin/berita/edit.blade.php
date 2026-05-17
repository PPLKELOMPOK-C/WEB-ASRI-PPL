@extends('layouts.app')

@section('title', 'Edit Berita')
@section('page-title', 'Edit Berita: ' . $news->judul)

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* Quill toolbar styling — sesuaikan warna hijau */
    .ql-toolbar.ql-snow {
        border: 1.5px solid #d1e7dd;
        border-bottom: 1px solid #d1e7dd;
        border-radius: 8px 8px 0 0;
        background: #f7fbf8;
        font-family: inherit;
    }
    .ql-container.ql-snow {
        border: 1.5px solid #d1e7dd;
        border-top: none;
        border-radius: 0 0 8px 8px;
        font-family: inherit;
        font-size: 15px;
        min-height: 320px;
    }
    .ql-editor {
        min-height: 300px;
        line-height: 1.75;
        color: #1a3a1f;
    }
    .ql-editor.ql-blank::before {
        color: #aac4b0;
        font-style: normal;
    }
    .ql-snow .ql-stroke { stroke: #3a7d4f; }
    .ql-snow .ql-fill  { fill:   #3a7d4f; }
    .ql-snow .ql-picker { color:  #3a7d4f; }
    .ql-snow .ql-picker-label { color: #3a7d4f; }
    .ql-snow.ql-toolbar button:hover .ql-stroke,
    .ql-snow .ql-toolbar button:hover .ql-stroke { stroke: var(--green-700, #2d6a3f); }
    .ql-snow.ql-toolbar button.ql-active .ql-stroke { stroke: var(--green-700, #2d6a3f); }
    .ql-snow.ql-toolbar button.ql-active { background: #d1e7dd; border-radius: 4px; }

    /* Upload area */
    .upload-area {
        border: 2px dashed #a8d5b5;
        padding: 24px 20px;
        border-radius: 12px;
        background: #f7fbf8;
        transition: border-color 0.2s, background 0.2s;
    }
    .upload-area:hover { border-color: var(--green-500, #4caf72); background: #eef7f1; }

    /* word count badge */
    .quill-meta {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 6px;
    }
    .quill-meta span {
        font-size: 12px;
        color: #7a9e86;
        background: #eef7f1;
        padding: 2px 10px;
        border-radius: 20px;
    }

    /* Current image container */
    .current-image-wrap {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
        padding: 14px;
        background: white;
        border-radius: 10px;
        border: 1px solid #d1e7dd;
    }
    .current-image-wrap img {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #d1e7dd;
    }
    .current-image-info { font-size: 13px; color: #5a7a5a; line-height: 1.6; }
    .current-image-info strong { color: var(--green-800, #1e4d2b); }
</style>
@endpush

@section('content')
<div style="max-width:900px;margin:0 auto">
    <div class="card">
        <div class="card-title">
            <i class="ri-edit-box-line" style="color:var(--green-600)"></i>
            Perbarui Informasi Berita
        </div>

        {{-- Error --}}
        @if($errors->any())
        <div style="background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px;font-size:14px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.berita.update', $news->id) }}" method="POST" enctype="multipart/form-data" id="newsForm">
            @csrf
            @method('PUT')
            <div style="display:grid;gap:22px">

                {{-- Judul --}}
                <div class="form-group">
                    <label class="form-label">Judul Berita <span style="color:red">*</span></label>
                    <input type="text" name="judul"
                           class="form-control @error('judul') is-invalid @enderror"
                           value="{{ old('judul', $news->judul) }}"
                           required>
                    @error('judul')
                    <small style="color:#dc2626">{{ $message }}</small>
                    @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                    {{-- Kategori --}}
                    <div class="form-group">
                        <label class="form-label">Kategori <span style="color:red">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="pengumuman"   @selected(old('kategori',$news->kategori)=='pengumuman')>Pengumuman</option>
                            <option value="kegiatan"     @selected(old('kategori',$news->kategori)=='kegiatan')>Kegiatan</option>
                            <option value="info_penting" @selected(old('kategori',$news->kategori)=='info_penting')>Info Penting</option>
                            <option value="promo"        @selected(old('kategori',$news->kategori)=='promo')>Promo</option>
                        </select>
                        @error('kategori')
                        <small style="color:#dc2626">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Status Publikasi --}}
                    <div class="form-group">
                        <label class="form-label">Status Publikasi <span style="color:red">*</span></label>
                        <select name="is_published" class="form-control" required>
                            <option value="1" @selected(old('is_published',$news->is_published ? '1' : '0')=='1')>Terbit (Published)</option>
                            <option value="0" @selected(old('is_published',$news->is_published ? '1' : '0')=='0')>Draft (Arsip)</option>
                        </select>
                    </div>
                </div>

                {{-- Gambar Cover --}}
                <div class="form-group">
                    <label class="form-label">Gambar Sampul</label>
                    <div class="upload-area" id="uploadArea">

                        {{-- Gambar saat ini --}}
                        @if($news->gambar_cover)
                        <div class="current-image-wrap" id="currentImageWrap">
                            <img src="{{ asset('storage/' . $news->gambar_cover) }}" alt="Cover saat ini" id="currentImg">
                            <div class="current-image-info">
                                <strong>Gambar saat ini</strong><br>
                                Biarkan kosong jika tidak ingin mengubah gambar.<br>
                                <span style="font-size:12px;color:#7a9e86">{{ basename($news->gambar_cover) }}</span>
                            </div>
                        </div>
                        @else
                        <div style="text-align:center;margin-bottom:12px">
                            <i class="ri-image-line" style="font-size:32px;color:#a8d5b5"></i>
                            <p style="font-size:13px;color:#7a9e86;margin:4px 0">Belum ada gambar sampul</p>
                        </div>
                        @endif

                        <div style="text-align:center">
                            <p style="margin:0 0 10px;font-size:14px;color:#5a7a5a">
                                <i class="ri-upload-cloud-line"></i>
                                Unggah gambar baru (opsional)
                            </p>
                            <input type="file" name="gambar_cover" id="gambarBerita"
                                   class="form-control"
                                   accept="image/*"
                                   onchange="previewImage(this)"
                                   style="max-width:320px;margin:0 auto">
                            <small style="display:block;color:#7a9e86;margin-top:8px">
                                Rekomendasi 1200×600px &middot; Maks. 3MB &middot; JPG, PNG, WEBP
                            </small>
                        </div>

                        {{-- Preview gambar baru --}}
                        <div id="imagePreview" style="margin-top:16px;display:none;text-align:center">
                            <p style="font-size:12px;color:var(--green-700);font-weight:600;margin-bottom:8px">
                                <i class="ri-check-line"></i> Preview gambar baru:
                            </p>
                            <img id="preview" src="#" alt="Preview"
                                 style="max-width:100%;max-height:240px;object-fit:cover;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                            <p id="fileName" style="font-size:12px;color:#7a9e86;margin-top:6px"></p>
                        </div>
                    </div>
                </div>

                {{-- Konten Rich Text --}}
                <div class="form-group">
                    <label class="form-label">Konten Berita <span style="color:red">*</span></label>

                    {{-- Textarea hidden, nilai diisi JS sebelum submit --}}
                    <textarea name="konten" id="konten" style="display:none">{{ old('konten', $news->konten) }}</textarea>

                    {{-- Quill editor --}}
                    <div id="quill-editor"></div>

                    <div class="quill-meta">
                        <span id="word-count">0 kata</span>
                        <span id="char-count">0 karakter</span>
                    </div>

                    @error('konten')
                    <small style="color:#dc2626;display:block;margin-top:4px">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div style="margin-top:30px;padding-top:20px;border-top:1px solid #eef7f1;display:flex;gap:12px;justify-content:flex-end">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary" style="padding:11px 24px">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" style="padding:11px 32px;background:var(--green-700)">
                    <i class="ri-save-line"></i> Perbarui Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// ── Quill Init ──────────────────────────────────────────────
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Tuliskan detail isi berita di sini...',
    modules: {
        toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ color: [] }, { background: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ indent: '-1' }, { indent: '+1' }],
            [{ align: [] }],
            ['link', 'image', 'video'], // Tombol link tetap ada di sini
            ['blockquote', 'code-block'],
            ['clean']
        ]
    }
});

// ── Kustomisasi Handler Link (Agar Ada Opsi Teks & URL) ──────
quill.getModule('toolbar').addHandler('link', function(value) {
    if (value) {
        // Ambil teks yang sedang diblok/diseleksi oleh user (jika ada)
        const range = quill.getSelection();
        let selectedText = '';
        
        if (range && range.length > 0) {
            selectedText = quill.getText(range.index, range.length);
        }

        // 1. Tanya Teks yang Ingin Ditampilkan
        const textToDisplay = prompt('Masukkan teks yang ingin ditampilkan untuk link ini:', selectedText || 'Klik di sini');
        
        // Jika user menekan tombol batal pada prompt teks
        if (textToDisplay === null) return; 

        // 2. Tanya URL Tujuan
        const href = prompt('Masukkan URL Link (Contoh: https://google.com):', 'https://');
        
        // Jika user menekan tombol batal pada prompt URL atau membiarkannya kosong
        if (!href || href === 'https://') return;

        // Eksekusi penyisipan link ke editor
        if (range && range.length > 0) {
            // Jika ada teks yang diblok sebelumnya, timpa dengan teks baru + link
            quill.insertText(range.index, textToDisplay, 'link', href);
            quill.deleteText(range.index + textToDisplay.length, range.length);
            quill.setSelection(range.index + textToDisplay.length);
        } else {
            // Jika tidak ada teks yang diblok, langsung selipkan di posisi kursor
            const currentIndex = range ? range.index : quill.getLength();
            quill.insertText(currentIndex, textToDisplay, 'link', href);
            quill.setSelection(currentIndex + textToDisplay.length);
        }
    } else {
        // Jika tombol link diklik saat kursor berada di link yang aktif, hapus link-nya
        quill.format('link', false);
    }
});

// Isi editor dari old value (validasi gagal)
const oldVal = document.getElementById('konten').value;
if (oldVal) quill.root.innerHTML = oldVal;

// Word & char counter
quill.on('text-change', updateCounts);
function updateCounts() {
    const text  = quill.getText().trim();
    const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    const chars = quill.getText().length - 1; // minus trailing newline
    document.getElementById('word-count').textContent  = words + ' kata';
    document.getElementById('char-count').textContent  = chars + ' karakter';
}

// Sync ke textarea sebelum submit
document.getElementById('newsForm').addEventListener('submit', function () {
    const html = quill.root.innerHTML;
    if (quill.getText().trim().length === 0) {
        document.getElementById('konten').value = '';
    } else {
        document.getElementById('konten').value = html;
    }
});

// ── Image Preview ────────────────────────────────────────────
function previewImage(input) {
    const preview   = document.getElementById('preview');
    const container = document.getElementById('imagePreview');
    const fileName  = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        const file   = input.files[0];
        const reader = new FileReader();
        reader.onload = e => {
            preview.src              = e.target.result;
            container.style.display  = 'block';
            fileName.textContent     = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        };
        reader.readAsDataURL(file);
    }
}

// Drag & Drop ke upload area
const uploadArea = document.getElementById('uploadArea');
uploadArea.addEventListener('dragover',  e => { e.preventDefault(); uploadArea.style.borderColor = 'var(--green-600)'; });
uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor = '#a8d5b5'; });
uploadArea.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.style.borderColor = '#a8d5b5';
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('gambarBerita');
        const dt    = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewImage(input);
    }
});
</script>
@endpush