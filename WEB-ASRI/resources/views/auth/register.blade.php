<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - ASRI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green-500:#2a9d5c; --green-600:#1e7c46; --green-700:#175f36;
            --green-800:#134b2b; --green-900:#0e3820;
            --cream-100:#fdf9ee; --cream-200:#faf2d7;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--cream-100); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:32px 16px; }
        .register-card { background:white; border-radius:20px; box-shadow:0 4px 40px rgba(14,56,32,0.12); width:100%; max-width:540px; overflow:hidden; }
        .register-header { background:linear-gradient(135deg,var(--green-900),var(--green-700)); padding:28px 36px; }
        .register-body { padding:36px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:var(--green-900); margin-bottom:6px; }
        .form-input { width:100%; padding:11px 14px; border:1.5px solid #d1ddd3; border-radius:9px; font-family:inherit; font-size:14px; color:#1a2e1a; background:white; outline:none; transition:border-color 0.2s; }
        .form-input:focus { border-color:var(--green-500); box-shadow:0 0 0 3px rgba(42,157,92,0.1); }
        .form-group { margin-bottom:16px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .error-msg { color:#e53e3e; font-size:12px; margin-top:4px; }
        .btn-register { width:100%; padding:13px; background:var(--green-700); color:white; border:none; border-radius:10px; font-size:15px; font-weight:700; font-family:inherit; cursor:pointer; transition:background 0.2s; margin-top:6px; }
        .btn-register:hover { background:var(--green-800); }
    </style>
</head>
<body>
<div class="register-card">
    <div class="register-header">
        <a href="{{ route('home') }}" style="text-decoration:none">
            <div style="font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--cream-200)">🌿 ASRI</div>
        </a>
        <div style="font-size:20px;font-weight:700;color:white;margin-top:14px">Buat Akun Baru</div>
        <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-top:3px">Daftar sebagai Calon Penghuni Rusun ASRI</div>
    </div>

    <div class="register-body">
        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#991b1b">
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}"
                    placeholder="Masukkan nama sesuai KTP" required autofocus>
                @error('name')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email <span style="color:red">*</span></label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                    placeholder="nama@email.com" required>
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">No. WhatsApp <span style="color:red">*</span></label>
                <input type="text" name="no_hp" class="form-input" value="{{ old('no_hp') }}"
                    placeholder="Contoh: 081234567890" maxlength="15" required>
                @error('no_hp')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Password <span style="color:red">*</span></label>
                    <div style="position:relative">
                        <input type="password" name="password" class="form-input" id="pwd1"
                            placeholder="Min. 8 karakter" required>
                        <button type="button" onclick="togglePwd('pwd1','eye1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af">
                            <i class="ri-eye-line" id="eye1" style="font-size:16px"></i>
                        </button>
                    </div>
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi <span style="color:red">*</span></label>
                    <div style="position:relative">
                        <input type="password" name="password_confirmation" class="form-input" id="pwd2"
                            placeholder="Ulangi password" required>
                        <button type="button" onclick="togglePwd('pwd2','eye2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af">
                            <i class="ri-eye-line" id="eye2" style="font-size:16px"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Password Strength --}}
            <div id="pwd-strength" style="margin-bottom:16px;display:none">
                <div style="height:5px;background:#e8f0eb;border-radius:3px;overflow:hidden">
                    <div id="strength-bar" style="height:100%;width:0%;border-radius:3px;transition:all 0.3s"></div>
                </div>
                <div id="strength-label" style="font-size:11px;margin-top:4px;color:#5a7a5a"></div>
            </div>

            <div style="background:var(--cream-100);border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#5a7a5a;line-height:1.7">
                Pastikan data yang Anda masukkan benar. NIK akan diminta nanti saat Anda melakukan pengajuan sewa unit.
            </div>

            <button type="submit" class="btn-register">
                <i class="ri-user-add-line"></i> Buat Akun
            </button>
        </form>

        <div style="text-align:center;margin-top:20px;font-size:14px;color:#5a7a5a">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:var(--green-700);font-weight:700;text-decoration:none;margin-left:4px">Masuk</a>
        </div>
    </div>
</div>

<script>
function togglePwd(id, iconId) {
    const input = document.getElementById(id);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'ri-eye-line' : 'ri-eye-off-line';
}

document.getElementById('pwd1').addEventListener('input', function () {
    const val = this.value;
    const wrap = document.getElementById('pwd-strength');
    const bar  = document.getElementById('strength-bar');
    const lbl  = document.getElementById('strength-label');
    wrap.style.display = val.length > 0 ? 'block' : 'none';

    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        {w:'25%', c:'#ef4444', t:'Lemah'},
        {w:'50%', c:'#f59e0b', t:'Cukup'},
        {w:'75%', c:'#3b82f6', t:'Baik'},
        {w:'100%',c:'#22c55e', t:'Kuat'},
    ];
    const lvl = levels[Math.max(0, score - 1)];
    bar.style.width      = lvl.w;
    bar.style.background = lvl.c;
    lbl.textContent      = 'Kekuatan: ' + lvl.t;
    lbl.style.color      = lvl.c;
});
</script>
</body>
</html>