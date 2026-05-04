@extends('layouts.app')

@section('title', 'Edit Unit Rusun')
@section('page-title', 'Manajemen Unit Rusun')

@section('content')
<div class="card">
    <div class="mb-6 flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--green-900);">Edit Unit: {{ $unit->no_kamar }}</h2>
            <p style="font-size: 13px; color: #5a7a5a;">Gedung {{ $unit->gedung }} - Blok {{ $unit->blok }}</p>
        </div>
        <a href="{{ route('admin.unit.index') }}" class="btn btn-secondary btn-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.unit.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-2">
            {{-- Bagian Kiri: Upload Gambar --}}
            <div class="form-group">
                <label class="form-label">Foto Unit</label>
                <div style="border: 2px dashed #d1ddd3; border-radius: 12px; padding: 20px; text-align: center; background: var(--green-50);">
                    @if($unit->gambar)
                        <img src="{{ asset('storage/' . $unit->gambar) }}" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    @else
                        <div style="height: 150px; display: flex; align-items: center; justify-content: center; color: var(--green-300);">
                            <i class="ri-image-add-line" style="font-size: 48px;"></i>
                        </div>
                    @endif
                    <input type="file" name="gambar" class="form-control">
                    <p style="font-size: 11px; color: #5a7a5a; margin-top: 8px;">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                </div>
            </div>

            {{-- Bagian Kanan: Input Data --}}
            <div class="grid">
                <div class="form-group">
                    <label class="form-label">Nama Gedung</label>
                    <input type="text" name="gedung" value="{{ old('gedung', $unit->gedung) }}" class="form-control" required>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Blok</label>
                        <input type="text" name="blok" value="{{ old('blok', $unit->blok) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Kamar</label>
                        <input type="text" name="no_kamar" value="{{ old('no_kamar', $unit->no_kamar) }}" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-3" style="margin-top: 15px;">
            <div class="form-group">
                <label class="form-label">Lantai</label>
                <input type="number" name="lantai" value="{{ old('lantai', $unit->lantai) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Luas (m²)</label>
                <input type="number" name="luas_m2" value="{{ old('luas_m2', $unit->luas_m2) }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Harga Sewa (Rp)</label>
                <input type="number" name="harga_sewa" value="{{ old('harga_sewa', intval($unit->harga_sewa)) }}" class="form-control" required>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Wilayah</label>
                <select name="wilayah" class="form-control" required>
                    @foreach(['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat'] as $w)
                        <option value="{{ $w }}" {{ $unit->wilayah == $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status Unit</label>
                <select name="status" class="form-control" required>
                    <option value="tersedia" {{ $unit->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dihuni" {{ $unit->status == 'dihuni' ? 'selected' : '' }}>Dihuni</option>
                    <option value="maintenance" {{ $unit->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $unit->alamat) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Fasilitas</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $unit->deskripsi) }}</textarea>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8f0eb; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
                <i class="ri-save-line"></i> Simpan Perubahan Unit
            </button>
        </div>
    </form>
</div>
@endsection