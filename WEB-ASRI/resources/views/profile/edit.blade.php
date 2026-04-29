@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Profil')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    /* Agar foto bulat sempurna dan di tengah */
    .profile-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover; /* INI KUNCINYA */
        object-position: center;
        border: 3px solid rgba(255,255,255,0.3);
    }
    .preview-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--green-600);
    }
    #cropper-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.85);
        align-items: center; justify-content: center;
    }
    .cropper-container-box {
        background: white; width: 90%; max-width: 450px; border-radius: 12px; overflow: hidden;
    }
    .img-container { max-height: 400px; width: 100%; background: #000; }
</style>
@endpush

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    
    {{-- Header Profil --}}
    <div class="card" style="margin-bottom: 24px; border: none; background: linear-gradient(135deg, var(--green-800) 0%, var(--green-600) 100%); color: white;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div id="header-avatar-container">
                @if($user->foto_profil)
                    <img id="header-preview" src="{{ asset('storage/' . $user->foto_profil) }}" class="profile-circle">
                @else
                    <div class="profile-circle" style="background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 40px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 style="margin: 0; font-family: 'Playfair Display', serif; color: var(--cream-200);">{{ $user->name }}</h2>
                <p style="margin: 5px 0 0; opacity: 0.8; font-size: 14px;">{{ $user->email }}</p>
                <div style="margin-top: 10px;">
                    <span class="badge" style="background: var(--green-400); color: white;">
                        <i class="ri-shield-check-line"></i> {{ ucfirst($user->role ?? 'Pengguna') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-title"><i class="ri-user-settings-line"></i> Informasi Dasar</div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('patch')

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label">Foto Profil</label>
                    
                    {{-- Preview Area --}}
                    <div id="new-preview-wrapper" style="margin-bottom: 12px;">
                        @if($user->foto_profil)
                            <img id="new-preview-img" src="{{ asset('storage/' . $user->foto_profil) }}" class="preview-circle">
                        @else
                            <div id="placeholder-preview" class="preview-circle" style="background: #eee; display: flex; align-items: center; justify-content: center; font-size: 30px; color: #ccc;">?</div>
                        @endif
                        <p id="preview-text" style="font-size: 11px; color: #666; margin-top: 4px;">Foto saat ini</p>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <input type="file" id="input-foto" style="display: none;" accept="image/*">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('input-foto').click()">
                            <i class="ri-image-edit-line"></i> Ganti/Adjust Foto
                        </button>
                        @if($user->foto_profil)
                            <button type="button" class="btn" onclick="deletePhotoNow()" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">Hapus</button>
                        @endif
                    </div>
                    <input type="hidden" name="foto_profil_cropped" id="foto_profil_cropped">
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Simpan Perubahan</button>
            </form>
        </div>
        
        {{-- Card Password Tetap Sama --}}
        <div class="card">
            <div class="card-title"><i class="ri-lock-password-line"></i> Keamanan Akun</div>
            <form method="post" action="{{ route('password.update') }}">
                @csrf @method('put')
                <div class="form-group"><label class="form-label">Password Baru</label><input type="password" name="password" class="form-control"></div>
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Ganti Password</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CROPPER --}}
<div id="cropper-modal">
    <div class="cropper-container-box">
        <div style="padding: 15px; font-weight: bold; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
            <span>Adjust Foto (Geser & Zoom)</span>
            <span style="cursor:pointer" onclick="closeModal()">&times;</span>
        </div>
        <div class="img-container">
            <img id="image-to-crop">
        </div>
        <div style="padding: 15px; display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" class="btn" onclick="closeModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="applyCrop()">Simpan</button>
        </div>
    </div>
</div>

<form id="form-hapus-foto" action="{{ route('profile.update') }}" method="POST" style="display:none;">
    @csrf @method('patch')
    <input type="hidden" name="delete_photo" value="1">
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;
    const modal = document.getElementById('cropper-modal');
    const image = document.getElementById('image-to-crop');
    const inputFoto = document.getElementById('input-foto');

    inputFoto.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                image.src = event.target.result;
                modal.style.display = 'flex';
                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: 1, // Memaksa kotak sempurna
                    viewMode: 1,    // Memaksa crop tetap di dalam gambar
                    autoCropArea: 1 // Mulai dari area maksimal
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });

    function closeModal() { modal.style.display = 'none'; inputFoto.value = ''; }

    function applyCrop() {
        // Ambil hasil crop dengan resolusi cukup (500x500)
        const canvas = cropper.getCroppedCanvas({ width: 500, height: 500 });
        const base64 = canvas.toDataURL('image/jpeg', 0.9);
        
        // Update input hidden
        document.getElementById('foto_profil_cropped').value = base64;
        
        // Update preview di form
        const previewImg = document.getElementById('new-preview-img');
        if(previewImg) {
            previewImg.src = base64;
        } else {
            // Jika sebelumnya tidak ada foto, ganti placeholder jadi img
            document.getElementById('placeholder-preview').outerHTML = `<img id="new-preview-img" src="${base64}" class="preview-circle">`;
        }
        
        // Update teks & preview header langsung (visual feedback)
        document.getElementById('preview-text').innerText = "Preview foto baru (belum disimpan)";
        document.getElementById('preview-text').style.color = "var(--green-700)";
        
        const headerPrev = document.getElementById('header-preview');
        if(headerPrev) headerPrev.src = base64;

        closeModal();
    }

    function deletePhotoNow() { if (confirm('Hapus foto profil?')) document.getElementById('form-hapus-foto').submit(); }
</script>
@endpush