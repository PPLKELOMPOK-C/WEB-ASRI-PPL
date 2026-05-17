@extends('layouts.app')
@section('title', 'Tambah Unit Baru')
@section('page-title', 'Tambah Unit Properti')

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.unit.index') }}" class="btn btn-light">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('admin.unit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            {{-- Bagian Kiri: Detail Lokasi --}}
            <div class="form-group-section">
                <h4 style="margin-bottom: 15px; color: var(--green-900); border-bottom: 1px solid #eee; padding-bottom: 5px;">Informasi Lokasi</h4>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Gedung / Tower <span style="color: red;">*</span></label>
                    <input type="text" name="gedung" class="form-control @error('gedung') is-invalid @enderror" value="{{ old('gedung') }}" placeholder="Contoh: Tower A" required>
                    @error('gedung') <small style="color: red;">{{ $message }}</small> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Blok <span style="color: red;">*</span></label>
                        <input type="text" name="blok" class="form-control @error('blok') is-invalid @enderror" value="{{ old('blok') }}" placeholder="A1" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Lantai <span style="color: red;">*</span></label>
                        <input type="number" name="lantai" class="form-control @error('lantai') is-invalid @enderror" value="{{ old('lantai') }}" placeholder="1" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>No. Kamar <span style="color: red;">*</span></label>
                    <input type="text" name="no_kamar" class="form-control @error('no_kamar') is-invalid @enderror" value="{{ old('no_kamar') }}" placeholder="01" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Wilayah <span style="color: red;">*</span></label>
                    <select name="wilayah" class="form-control @error('wilayah') is-invalid @enderror" required>
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach(['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat'] as $w)
                            <option value="{{ $w }}" {{ old('wilayah') == $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap <span style="color: red;">*</span></label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
                </div>
            </div>

            {{-- Bagian Kanan: Detail Properti & Harga --}}
            <div class="form-group-section">
                <h4 style="margin-bottom: 15px; color: var(--green-900); border-bottom: 1px solid #eee; padding-bottom: 5px;">Spesifikasi & Harga</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Harga Sewa (Rp) <span style="color: red;">*</span></label>
                        <input type="number" name="harga_sewa" class="form-control @error('harga_sewa') is-invalid @enderror" value="{{ old('harga_sewa') }}" placeholder="1500000" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Luas (m²) </label>
                        <input type="number" name="luas_m2" class="form-control @error('luas_m2') is-invalid @enderror" value="{{ old('luas_m2') }}" placeholder="36">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Status Awal <span style="color: red;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="dihuni" {{ old('status') == 'dihuni' ? 'selected' : '' }}>Dihuni</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi Unit</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Foto Unit</label>
                    <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                    <small style="color: #888;">Format: JPG, PNG, WEBP (Maks 2MB)</small>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; text-align: right;">
            <button type="reset" class="btn btn-light">Reset</button>
            <button type="submit" class="btn btn-primary" style="padding-left: 30px; padding-right: 30px;">
                <i class="ri-save-line"></i> Simpan Unit
            </button>
        </div>
    </form>
</div>
@endsection