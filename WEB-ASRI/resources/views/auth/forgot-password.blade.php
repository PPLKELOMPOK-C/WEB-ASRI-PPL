<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - ASRI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --green-600:#1e7c46; --green-700:#175f36; --green-800:#134b2b; --green-900:#0e3820; --cream-100:#fdf9ee; }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream-100);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
    </style>
</head>
<body>
<div style="background:white;border-radius:16px;box-shadow:0 4px 32px rgba(14,56,32,0.1);width:100%;max-width:440px;padding:40px">
    <a href="{{ route('home') }}" style="text-decoration:none">
        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--green-900);margin-bottom:28px">🌿 ASRI</div>
    </a>

    <div style="font-size:22px;font-weight:700;color:var(--green-900);margin-bottom:6px">Lupa Password?</div>
    <p style="font-size:14px;color:#5a7a5a;margin-bottom:24px;line-height:1.6">
        Masukkan email Anda. Kami akan mengirim link reset password.
    </p>

    @if(session('status'))
    <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#166534;display:flex;gap:8px">
        <i class="ri-checkbox-circle-line"></i> {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--green-900);margin-bottom:6px">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                placeholder="nama@email.com"
                style="width:100%;padding:12px 14px;border:1.5px solid #d1ddd3;border-radius:9px;font-family:inherit;font-size:14px;outline:none">
            @error('email')<div style="color:#e53e3e;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

        <button type="submit" style="width:100%;padding:13px;background:var(--green-700);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;font-family:inherit;cursor:pointer">
            Kirim Link Reset
        </button>
    </form>

    <div style="text-align:center;margin-top:20px">
        <a href="{{ route('login') }}" style="font-size:13px;color:#5a7a5a;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="ri-arrow-left-line"></i> Kembali ke Login
        </a>
    </div>
</div>
</body>
</html>
