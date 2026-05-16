@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link href="https://unpkg.com/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Injector Font Global Khusus Halaman Profil ASRI */
    .asri-profile-wrapper *:not(i) {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        box-sizing: border-box;
    }

    /* Core Layout: Lebar penuh sejajar lurus dengan batas menu atas (5%) */
    .asri-profile-wrapper {
        background-color: #FDFDFB; 
        min-height: 100vh; 
        padding: 40px 5%; 
        width: 100%;
    }

    /* Card Styling */
    .asri-card { 
        border-radius: 20px; 
        padding: 32px; 
        border: 1px solid #e8f0eb; 
    }
    
    .header-gradient { 
        background: linear-gradient(135deg, #0e3820 0%, #164d2d 100%); 
        box-shadow: 0 12px 30px rgba(14, 56, 32, 0.12); 
        margin-bottom: 32px;
        border: none;
    }
    
    .white-card { 
        background: white; 
        box-shadow: 0 4px 20px rgba(14, 56, 32, 0.02); 
    }
    
    /* Avatar Component & Hover Effect */
    .avatar-container { 
        width: 130px; 
        height: 130px; 
        border-radius: 50%; 
        border: 4px solid rgba(255, 255, 255, 0.2); 
        overflow: hidden; 
        background: #fff;
        transition: all 0.3s ease;
    }
    
    .avatar-img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    
    .camera-btn { 
        position: absolute; 
        bottom: 0px; 
        right: 0px; 
        background: white; 
        color: #0e3820; 
        width: 38px; 
        height: 38px; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        cursor: pointer; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        transition: all 0.2s ease; 
    }
    .camera-btn:hover { 
        transform: scale(1.1); 
        background: #f8fbf9;
    }
    
    .delete-avatar-btn { 
        position: absolute; 
        top: -4px; 
        left: -4px; 
        background: #ef4444; 
        color: white; 
        border: 2px solid #0e3820; 
        border-radius: 50%; 
        width: 26px; 
        height: 26px; 
        cursor: pointer; 
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .delete-avatar-btn:hover { transform: scale(1.1); background: #dc2626; }
    
    .badge { 
        background: rgba(255, 255, 255, 0.12); 
        padding: 6px 14px; 
        border-radius: 50px; 
        font-size: 13px; 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        margin-top: 12px; 
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Grid Flexibilitas Lebar Penuh */
    .asri-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); 
        gap: 32px; 
        width: 100%;
    }
    
    @media (max-width: 640px) {
        .asri-grid { grid-template-columns: 1fr; }
    }

    .form-title { 
        margin: 0 0 24px 0; 
        font-size: 18px; 
        color: #0e3820; 
        font-weight: 700; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    
    /* Inputs Form Standard */
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .asri-input { 
        width: 100%; 
        padding: 12px 16px; 
        border: 1.5px solid #e2e8f0; 
        border-radius: 12px; 
        transition: all 0.25s ease; 
        background: #fdfdfd; 
        font-size: 14px;
        color: #0e3820;
    }
    .asri-input:focus { 
        border-color: #164d2d; 
        outline: none; 
        background: white;
        box-shadow: 0 0 0 4px rgba(22, 77, 45, 0.06); 
    }

    /* Buttons Premium Khas ASRI */
    .btn-primary { 
        width: 100%; 
        padding: 14px; 
        background: #0e3820; 
        color: white; 
        border: none; 
        border-radius: 12px; 
        font-weight: 700; 
        font-size: 14px;
        cursor: pointer; 
        transition: all 0.2s ease; 
    }
    .btn-primary:hover { background: #164d2d; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,56,32,0.15); }
    
    .btn-secondary { 
        width: 100%; 
        padding: 14px; 
        background: #f1f5f9; 
        color: #334155; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        font-weight: 700; 
        font-size: 14px;
        cursor: pointer; 
        transition: all 0.2s ease;
    }
    .btn-secondary:hover { background: #e2e8f0; color: #0e3820; }

    /* Toast Notification Premium */
    .asri-toast { 
        position: fixed; 
        top: 30px; 
        right: 30px; 
        background: white; 
        padding: 16px 24px; 
        border-radius: 14px; 
        box-shadow: 0 12px 36px rgba(14,56,32,0.12); 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        z-index: 9999; 
        animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); 
        border-left: 6px solid #22c55e; 
    }

    /* Modal Styling */
    .modal-overlay { 
        display: none; 
        position: fixed; 
        inset: 0; 
        background: rgba(9, 28, 16, 0.8); 
        backdrop-filter: blur(6px); 
        z-index: 10000; 
        align-items: center; 
        justify-content: center; 
    }
    .modal-content { 
        background: white; 
        padding: 32px; 
        border-radius: 24px; 
        width: 90%; 
        max-width: 480px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }
    .crop-area { border-radius: 16px; overflow: hidden; background: #f8fafc; margin: 20px 0; max-height: 320px; }
    
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-save { background: #0e3820; color: white; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #164d2d; }
    .btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }
    .btn-cancel { background: #f1f5f9; color: #64748b; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; }

    @keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div class="asri-profile-wrapper">
    <div style="width: 100%;">

        {{-- Toast Sukses --}}
        @if (session('status'))
        <div id="status-alert" class="asri-toast">
            <i class="ri-checkbox-circle-fill" style="color: #22c55e; font-size: 22px;"></i>
            <span style="font-size: 14px; font-weight: 600; color: #0e3820;">
                @if(session('status') == 'profile-updated') Profil diperbarui! @endif
                @if(session('status') == 'password-updated') Password berhasil diganti! @endif
                @if(session('status') == 'avatar-deleted') Foto profil dihapus. @endif
            </span>
        </div>
        @endif

        {{-- Toast Error (Khusus Error dari updatePassword Bag) --}}
        @if ($errors->updatePassword->any())
        <div id="error-alert" class="asri-toast" style="border-left: 6px solid #ef4444;">
            <i class="ri-error-warning-fill" style="color: #ef4444; font-size: 22px;"></i>
            <div style="display: flex; flex-direction: column; gap: 2px;">
                @foreach ($errors->updatePassword->all() as $error)
                    <span style="font-size: 13px; font-weight: 600; color: #7f1d1d;">{{ $error }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Header Card --}}
        <div class="asri-card header-gradient">
            <div style="display: flex; align-items: center; gap: 32px; flex-wrap: wrap;">
                <div style="position: relative;">
                    <div class="avatar-container">
                        <img id="avatar-main" 
                             src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E8F5ED&color=0e3820&bold=true' }}" 
                             class="avatar-img">
                    </div>
                    
                    <label for="avatar-input" class="camera-btn" title="Ubah foto">
                        <i class="ri-camera-3-line" style="font-size: 18px;"></i>
                        <input type="file" id="avatar-input" hidden accept="image/*">
                    </label>

                    @if($user->avatar)
                    <form action="{{ route('profile.avatar.destroy') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="delete-avatar-btn" title="Hapus foto" onclick="return confirm('Hapus foto profil?')">
                            <i class="ri-delete-bin-line" style="font-size: 13px;"></i>
                        </button>
                    </form>
                    @endif
                </div>

                <div style="color: white;">
                    <h2 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">{{ $user->name }}</h2>
                    <p style="opacity: 0.8; margin: 6px 0 0 0; font-size: 15px;">{{ $user->email }}</p>
                    <div class="badge">
                        <i class="ri-shield-user-line"></i> {{ $user->username ?? 'Calon Penghuni' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Form --}}
        <div class="asri-grid">
            {{-- Account Info --}}
            <div class="asri-card white-card">
                <h3 class="form-title"><i class="ri-user-settings-line"></i> Informasi Akun</h3>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="asri-input" required>
                    </div>
                    <div style="margin-top: 32px;">
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            {{-- Password Change (Diberi Atribut required) --}}
            <div class="asri-card white-card">
                <h3 class="form-title"><i class="ri-lock-password-line"></i> Ganti Keamanan Password</h3>
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="input-group">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="••••••••" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="asri-input" required>
                    </div>
                    <div style="margin-top: 32px;">
                        <button type="submit" class="btn-secondary">Update Kata Sandi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cropper --}}
<div id="cropper-modal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin:0; font-size:18px; color:#0e3820; font-weight:700; display:flex; align-items:center; gap:8px;">
            <i class="ri-crop-2-line" style="color: #164d2d"></i> Sesuaikan Foto Profil
        </h3>
        <p style="font-size: 13px; color: #64748b; margin: 6px 0 0 0;">Geser dan sesuaikan lingkaran tengah untuk memotong foto Anda.</p>
        
        <div class="crop-area">
            <img id="image-to-crop" style="max-width: 100%; display: block;">
        </div>
        
        <div class="modal-footer">
            <button onclick="closeCropper()" class="btn-cancel">Batal</button>
            <button id="btn-save-crop" class="btn-save">
                <i class="ri-save-3-line" id="icon-save"></i> <span id="save-text">Pasang Foto</span>
            </button>
        </div>
    </div>
</div>

<script>
let cropper;
const avatarInput = document.getElementById('avatar-input');
const modal = document.getElementById('cropper-modal');
const imageToCrop = document.getElementById('image-to-crop');

avatarInput.addEventListener('change', function(e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageToCrop.src = event.target.result;
            modal.style.display = 'flex';
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, { 
                aspectRatio: 1, 
                viewMode: 1,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false
            });
        };
        reader.readAsDataURL(files[0]);
    }
});

document.getElementById('btn-save-crop').addEventListener('click', function() {
    const btn = this;
    const text = document.getElementById('save-text');
    const icon = document.getElementById('icon-save');
    
    btn.disabled = true;
    text.innerText = "Mengunggah...";
    if(icon) icon.className = "ri-loader-4-line ri-spin";

    cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob(function(blob) {
        const formData = new FormData();
        formData.append('avatar', blob, 'avatar.jpg');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch("{{ route('profile.avatar.update') }}", {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Gagal memperbarui foto profil.');
                resetBtnState();
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan jaringan.');
            resetBtnState();
        });
    }, 'image/jpeg');

    function resetBtnState() {
        btn.disabled = false;
        text.innerText = "Pasang Foto";
        if(icon) icon.className = "ri-save-3-line";
    }
});

function closeCropper() {
    modal.style.display = 'none';
    avatarInput.value = '';
}

// Fade out alert otomatis setelah 4 detik (Berlaku untuk Toast Sukses & Error)
setTimeout(() => { 
    const alertSuccess = document.getElementById('status-alert');
    const alertError = document.getElementById('error-alert');
    
    if(alertSuccess) {
        alertSuccess.style.transition = 'opacity 0.5s ease';
        alertSuccess.style.opacity = '0';
        setTimeout(() => alertSuccess.remove(), 500);
    }
    if(alertError) {
        alertError.style.transition = 'opacity 0.5s ease';
        alertError.style.opacity = '0';
        setTimeout(() => alertError.remove(), 500);
    }
}, 4000);
</script>
@endsection