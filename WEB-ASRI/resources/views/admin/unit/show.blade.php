@extends('layouts.app')

@section('title', 'Detail Unit Rusun')
@section('page-title', 'Detail Unit')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 700; color: var(--green-900);">Unit {{ $unit->no_kamar }}</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.unit.index') }}" class="btn btn-secondary btn-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <a href="{{ route('admin.unit.edit', $unit->id) }}" class="btn btn-primary btn-sm" style="background: #ca8a04;">
                <i class="ri-edit-line"></i> Edit Unit
            </a>
        </div>
    </div>

    <div class="grid grid-2">
        {{-- Foto Unit --}}
        <div>
            @if($unit->gambar)
                <img src="{{ asset('storage/' . $unit->gambar) }}" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            @else
                <div style="width: 100%; height: 300px; background: var(--green-50); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--green-300); border: 2px dashed #d1ddd3;">
                    <i class="ri-image-line" style="font-size: 64px;"></i>
                    <p style="margin-top: 10px; font-weight: 600;">Belum ada foto unit</p>
                </div>
            @endif
        </div>

        {{-- Informasi Detail --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="padding: 16px; background: var(--cream-100); border-radius: 10px; border-left: 4px solid var(--green-500);">
                <span style="font-size: 12px; text-transform: uppercase; color: #5a7a5a; font-weight: 700;">Status Saat Ini</span>
                <div style="margin-top: 5px;">
                    @if($unit->status == 'tersedia')
                        <span class="badge badge-success">Tersedia</span>
                    @elseif($unit->status == 'dihuni')
                        <span class="badge badge-info">Terisi (Dihuni)</span>
                    @else
                        <span class="badge badge-warning">Maintenance</span>
                    @endif
                </div>
            </div>

            <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
                <tr>
                    <td style="width: 40%; color: #5a7a5a; font-weight: 600;">Gedung / Blok</td>
                    <td style="font-weight: 700;">{{ $unit->gedung }} / {{ $unit->blok }}</td>
                </tr>
                <tr>
                    <td style="color: #5a7a5a; font-weight: 600;">Lantai</td>
                    <td>Lantai {{ $unit->lantai }}</td>
                </tr>
                <tr>
                    <td style="color: #5a7a5a; font-weight: 600;">Luas Unit</td>
                    <td>{{ $unit->luas_m2 ?? '-' }} m²</td>
                </tr>
                <tr>
                    <td style="color: #5a7a5a; font-weight: 600;">Wilayah</td>
                    <td>{{ $unit->wilayah }}</td>
                </tr>
                <tr>
                    <td style="color: #5a7a5a; font-weight: 600;">Harga Sewa</td>
                    <td style="color: var(--green-600); font-weight: 800; font-size: 16px;">
                        Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }} <small style="font-weight: 400; color: #5a7a5a;">/ bulan</small>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 10px;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--green-900); margin-bottom: 5px;">Alamat</h4>
                <p style="font-size: 14px; line-height: 1.6; color: #2d3d2d;">{{ $unit->alamat }}</p>
            </div>

            <div style="margin-top: 10px;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--green-900); margin-bottom: 5px;">Deskripsi Fasilitas</h4>
                <p style="font-size: 14px; line-height: 1.6; color: #2d3d2d;">{{ $unit->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection