@extends('layouts.app')

@section('title', 'Detail Penghuni')
@section('page-title', 'Profil & Dokumen Penghuni')

@section('content')
{{-- Notifikasi Sukses/Error --}}
@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <i class="ri-error-warning-line"></i> {{ session('error') }}
    </div>
@endif

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('admin.penghuni.index') }}" class="btn btn-secondary" style="background: white; border: 1px solid #ddd; color: #333;">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
    <span style="font-size: 13px; color: #666;">ID Penghuni: #{{ $penghuni->id }}</span>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start;">
    
    {{-- Kolom Kiri: Profil & Form Kick --}}
    <div>
        <div class="card">
            <div style="text-align: center; padding: 10px 0;">
                <div style="width: 90px; height: 90px; background: var(--green-600); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 35px; margin: 0 auto 15px; border: 4px solid #f0fdf4;">
                    {{ strtoupper(substr($penghuni->name, 0, 1)) }}
                </div>
                <h3 style="margin-bottom: 5px;">{{ $penghuni->name }}</h3>
                <div style="display: flex; justify-content: center; gap: 5px;">
                    <span class="badge" style="background: #e1f5fe; color: #01579b; padding: 4px 10px; border-radius: 20px; font-size: 11px;">
                        <i class="ri-user-follow-line"></i> {{ ucfirst($penghuni->role) }}
                    </span>
                    @if($penghuni->is_active)
                        <span class="badge" style="background: #f0fdf4; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px;">Aktif</span>
                    @endif
                </div>
            </div>

                    <div style="margin-top: 25px; font-size: 14px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label ...>Nomor NIK</label>
                    <div ...>
                        {{ $penghuni->nik ?? (optional($penghuni->pengajuanSewas->first())->nik ?? 'Belum diatur') }}
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <label ...>Nomor WhatsApp</label>
                    <div ...>
                        {{ $penghuni->no_hp ?? (optional($penghuni->pengajuanSewas->first())->no_hp ?? '-') }}
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #64748b; display: block; font-size: 12px; text-transform: uppercase; font-weight: bold;">Email Address</label>
                    <div style="font-weight: 600; color: #1e293b;">{{ $penghuni->email }}</div>
                </div>
            </div>

            {{-- Fitur Kick --}}
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px dashed #fee2e2;">
                <h5 style="color: #991b1b; margin-bottom: 10px; font-size: 14px;"><i class="ri-error-warning-fill"></i> Area Bahaya</h5>
                <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Mengubah kembali status menjadi Calon Penghuni akan menghentikan akses fitur penghuni secara otomatis.</p>
                
                <form action="{{ route('admin.penghuni.kick', $penghuni->id) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin me-kick {{ $penghuni->name }}? Status akan berubah kembali menjadi Calon Penghuni.')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn" style="width: 100%; background: #ef4444; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="ri-user-unfollow-line"></i> Kick Penghuni
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Dokumen --}}
    <div>
        <div class="card">
            <div class="card-title" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <div style="background: var(--green-600); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="ri-shield-check-line"></i>
                </div>
                <span style="font-weight: bold; font-size: 18px;">Verifikasi Berkas Digital</span>
            </div>

            @php
                $berkasList = [
                    ['id' => 'ktp', 'label' => 'Kartu Tanda Penduduk (KTP)', 'desc' => 'Pastikan NIK sesuai dengan profil.'],
                    ['id' => 'kk', 'label' => 'Kartu Keluarga (KK)', 'desc' => 'Digunakan untuk validasi jumlah anggota keluarga.'],
                    ['id' => 'slip_gaji', 'label' => 'Slip Gaji / Penghasilan', 'desc' => 'Bukti kemampuan bayar bulanan.'],
                ];
            @endphp

            <div style="display: grid; gap: 15px;">
                @foreach($berkasList as $item)
                    @php 
                        // Menggunakan relasi hasManyThrough yang sudah kita buat
                        $file = $penghuni->dokumens->where('jenis_dokumen', $item['id'])->first(); 
                    @endphp

                    <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; background: {{ $file ? '#fff' : '#f8fafc' }}; transition: all 0.3s;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="font-size: 30px; color: {{ $file ? 'var(--green-600)' : '#cbd5e1' }};">
                                <i class="{{ $file ? 'ri-file-check-fill' : 'ri-file-warning-line' }}"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0; font-size: 15px; color: #1e293b;">{{ $item['label'] }}</h4>
                                <p style="margin: 0; font-size: 12px; color: #64748b;">{{ $item['desc'] }}</p>
                            </div>
                        </div>

                        <div>
                            @if($file)
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('admin.penghuni.view', [$penghuni->id, $item['id']]) }}" target="_blank" class="btn btn-sm btn-secondary" style="border: 1px solid #ddd;">
                                        <i class="ri-eye-line"></i> Lihat
                                    </a>
                                    <a href="{{ route('admin.penghuni.download', [$penghuni->id, $item['id']]) }}" class="btn btn-sm btn-primary">
                                        <i class="ri-download-2-line"></i> Unduh
                                    </a>
                                </div>
                            @else
                                <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Berkas Kosong</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Catatan Info --}}
            <div style="margin-top: 30px; background: #fff9db; padding: 15px; border-radius: 8px; border-left: 4px solid #fab005;">
                <div style="display: flex; gap: 10px;">
                    <i class="ri-information-line" style="color: #f08c00; font-size: 20px;"></i>
                    <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.5;">
                        <strong>Catatan Admin:</strong> Dokumen di atas diambil dari histori pengajuan sewa terbaru milik penghuni ini. Jika dokumen sudah kedaluwarsa, mintalah penghuni untuk mengunggah ulang melalui menu profil mereka.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection