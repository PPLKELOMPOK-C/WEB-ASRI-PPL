<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ASRI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green-50:#f0faf4; --green-100:#dcf4e7; --green-200:#b8e9cf;
            --green-500:#2a9d5c; --green-600:#1e7c46; --green-700:#175f36;
            --green-800:#134b2b; --green-900:#0e3820;
            --cream-100:#fdf9ee; --cream-200:#faf2d7;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            min-height:100vh;
            display:grid;
            grid-template-columns:1fr 1fr;
        }
        /* Panel Kiri - Dekoratif */
        .left-panel {
            background:linear-gradient(160deg, var(--green-900) 0%, var(--green-700) 60%, var(--green-500) 100%);
            display:flex; flex-direction:column; justify-content:space-between;
            padding:48px 52px; position:relative; overflow:hidden;
        }
        .left-panel::before {
            content:''; position:absolute; top:-120px; right:-80px;
            width:400px; height:400px; border-radius:50%;
            background:rgba(255,255,255,0.05);
        }
        .left-panel::after {
            content:''; position:absolute; bottom:-100px; left:-60px;
            width:300px; height:300px; border-radius:50%;
            background:rgba(255,255,255,0.04);
        }
        /* Panel Kanan - Form */
        .right-panel {
            background:var(--cream-100);
            display:flex; align-items:center; justify-content:center;
            padding:48px;
        }
        .form-box { width:100%; max-width:420px; }
        .form-title {
            font-family:'Playfair Display',serif;
            font-size:28px; font-weight:700;
            color:var(--green-900); margin-bottom:6px;
        }
        .form-subtitle { font-size:14px; color:#5a7a5a; margin-bottom:32px; }
        .input-wrap { position:relative; }
        .input-icon {
            position:absolute; left:14px; top:50%; transform:translateY(-50%);
            color:#9ca3af; font-size:18px; pointer-events:none;
        }
        .form-input {
            width:100%; padding:12px 14px 12px 42px;
            border:1.5px solid #d1ddd3; border-radius:10px;
            font-family:inherit; font-size:14px; color:#1a2e1a;
            background:white; outline:none; transition:border-color 0.2s;
        }
        .form-input:focus { border-color:var(--green-500); box-shadow:0 0 0 3px rgba(42,157,92,0.1); }
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:var(--green-900); margin-bottom:7px; }
        .btn-login {
            width:100%; padding:14px; background:var(--green-700); color:white;
            border:none; border-radius:10px; font-size:15px; font-weight:700;
            font-family:inherit; cursor:pointer; transition:background 0.2s;
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-login:hover { background:var(--green-800); }
        .error-msg { color:#e53e3e; font-size:12px; margin-top:5px; }
        @media(max-width:768px) {
            body { grid-template-columns:1fr; }
            .left-panel { display:none; }
            .right-panel { padding:32px 24px; }
        }
    </style>
</head>
<body>

{{-- Panel Kiri --}}
<div class="left-panel">
    <div style="position:relative;z-index:1">
        <a href="{{ route('home') }}" style="text-decoration:none">
            <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--cream-200)">🌿 ASRI</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;letter-spacing:1px;text-transform:uppercase">Sistem Manajemen Rusun</div>
        </a>
    </div>

    <div style="position:relative;z-index:1">
        <h2 style="font-family:'Playfair Display',serif;font-size:36px;font-weight:700;color:white;line-height:1.3;margin-bottom:16px">
            Selamat Datang<br>Kembali
        </h2>
        <p style="font-size:15px;color:rgba(255,255,255,0.75);line-height:1.7;max-width:340px">
            Kelola hunian Anda dengan mudah. Pantau tagihan, laporkan kerusakan, dan akses semua layanan rusun secara digital.
        </p>

        <div style="margin-top:36px;display:grid;gap:14px">
            @foreach([['ri-shield-check-line','Aman & Terenkripsi','Data pribadi Anda terlindungi'],['ri-smartphone-line','Akses Kapan Saja','Tersedia 24 jam via web & mobile'],['ri-customer-service-2-line','Dukungan Admin','Tim kami siap membantu Anda']] as [$ic,$t,$d])
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="{{ $ic }}" style="font-size:18px;color:rgba(255,255,255,0.9)"></i>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:white">{{ $t }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:1px">{{ $d }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="position:relative;z-index:1;font-size:12px;color:rgba(255,255,255,0.4)">
        © {{ date('Y') }} ASRI · Rusun Jakarta
    </div>
</div>

{{-- Panel Kanan - Form --}}
<div class="right-panel">
    <div class="form-box">
        <div class="form-title">Masuk ke Akun</div>
        <div class="form-subtitle">Masukkan email dan password Anda</div>

        {{-- Session Error --}}
        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#991b1b;display:flex;gap:8px;align-items:flex-start">
            <i class="ri-close-circle-line" style="font-size:16px;flex-shrink:0;margin-top:1px"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#166534">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrap">
                    <i class="ri-mail-line input-icon"></i>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                        placeholder="nama@email.com" required autofocus autocomplete="email">
                </div>
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
                    <label class="form-label" style="margin-bottom:0">Password</label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:12px;color:var(--green-600);text-decoration:none;font-weight:600">Lupa password?</a>
                    @endif
                </div>
                <div class="input-wrap">
                    <i class="ri-lock-line input-icon"></i>
                    <input type="password" name="password" class="form-input" id="passwordInput"
                        placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" onclick="togglePwd()" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:16px">
                        <i class="ri-eye-line" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px">
                <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;accent-color:var(--green-600);cursor:pointer">
                <label for="remember" style="font-size:13px;color:#5a7a5a;cursor:pointer">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="ri-login-box-line"></i> Masuk
            </button>
        </form>

        @if(Route::has('register'))
        <div style="text-align:center;margin-top:24px;font-size:14px;color:#5a7a5a">
            Belum punya akun?
            <a href="{{ route('register') }}" style="color:var(--green-700);font-weight:700;text-decoration:none;margin-left:4px">Daftar Sekarang</a>
        </div>
        @endif

        <div style="margin-top:28px;border-top:1px solid #e8f0eb;padding-top:20px;text-align:center">
            <a href="{{ route('home') }}" style="font-size:13px;color:#5a7a5a;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                <i class="ri-arrow-left-line"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ri-eye-off-line';
    } else {
        input.type = 'password';
        icon.className = 'ri-eye-line';
    }
}
</script>
</body>
</html>
