@extends('layouts.app')
@section('title','Lapor Kerusakan')
@section('page-title','Buat Laporan Kerusakan')

@section('content')
<div style="max-width:640px">
<div class="card">
    <div class="card-title"><i class="ri-tools-line" style="color:var(--green-500)"></i> Form Laporan Kerusakan</div>

    <div style="display:flex;align-items:center;gap:10px;padding:12px;background:var(--green-50);border-radius:8px;margin-bottom:20px">
        <i class="ri-building-line" style="color:var(--green-600)"></i>
        <div style="font-size:13px"><strong style="color:var(--green-900)">Unit:</strong> {{ $kontrak->unit->nama_unit }} · {{ $kontrak->unit->gedung }}</div>
    </div>

    <form method="POST" action="{{ route('penghuni.laporan.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">Kategori Kerusakan <span style="color:red">*</span></label>
            <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $val => $label)
                <option value="{{ $val }}" {{ old('kategori')==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('kategori')<div style="color:#e53e3e;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Judul Laporan <span style="color:red">*</span></label>
            <input type="text" name="judul" class="form-control" value="{{ old('judul') }}" placeholder="Contoh: Keran kamar mandi bocor" required minlength="5" maxlength="100">
            @error('judul')<div style="color:#e53e3e;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Masalah <span style="color:red">*</span></label>
            <textarea name="deskripsi" class="form-control" rows="5" required minlength="20" maxlength="1000" placeholder="Deskripsikan kerusakan secara detail: lokasi, kapan terjadi, dampaknya, dll.">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<div style="color:#e53e3e;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Foto Kerusakan (Opsional)</label>
            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
            <div style="font-size:12px;color:#5a7a5a;margin-top:4px">Format: JPG/PNG, maks 3MB. Foto membantu teknisi menilai kerusakan.</div>
            <img id="foto-preview" src="" style="display:none;max-height:220px;margin-top:10px;border-radius:8px;border:1px solid #e8f0eb">
        </div>

        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary"><i class="ri-send-plane-line"></i> Kirim Laporan</button>
            <a href="{{ route('penghuni.laporan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</div>

@push('scripts')
<script>
function previewFoto(i){
    const p=document.getElementById('foto-preview');
    if(i.files&&i.files[0]){p.src=URL.createObjectURL(i.files[0]);p.style.display='block';}
}
</script>
@endpush
@endsection
