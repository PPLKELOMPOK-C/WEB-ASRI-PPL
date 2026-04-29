@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Detail Pengajuan: #' . $pengajuan->id)

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    {{-- Kolom Kiri: Informasi Utama --}}
    <div>
        {{-- Status Card --}}
        <div class="card" style="margin-bottom: 24px; border-left: 5px solid 
            @if($pengajuan->status == 'pending') #f59e0b 
            @elseif($pengajuan->status == 'diterima') var(--green-600) 
            @elseif($pengajuan->status == 'ditolak') #dc2626 
            @else var(--green-400) @endif">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #666;">Status Saat Ini</span>
                    <h3 style="margin: 5px 0 0; color: var(--green-900);">{{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}</h3>
                </div>
                <div class="badge" style="background: var(--green-50); color: var(--green-700); padding: 10px 15px;">
                    <i class="ri-calendar-line"></i> Diajukan: {{ $pengajuan->created_at->format('d M Y') }}
                </div>
            </div>
        </div>

        {{-- Data Pemohon & Unit --}}
        <div class="grid grid-2" style="margin-bottom: 24px;">
            <div class="card">
                <div class="card-title"><i class="ri-user-line"></i> Informasi Pemohon</div>
                <table style="width: 100%; font-size: 14px; border-collapse: separate; border-spacing: 0 10px;">
                    <tr><td style="color: #666; width: 40%;">Nama</td><td><strong>{{ $pengajuan->user->name }}</strong></td></tr>
                    <tr><td style="color: #666;">Email</td><td>{{ $pengajuan->user->email }}</td></tr>
                    <tr><td style="color: #666;">NIK</td><td><code>{{ $pengajuan->nik }}</code></td></tr>
                    <tr><td style="color: #666;">No. HP</td><td>{{ $pengajuan->no_hp }}</td></tr>
                </table>
            </div>
            <div class="card">
                <div class="card-title"><i class="ri-building-line"></i> Detail Unit</div>
                <table style="width: 100%; font-size: 14px; border-collapse: separate; border-spacing: 0 10px;">
                    <tr><td style="color: #666; width: 40%;">Unit</td><td><strong>{{ $pengajuan->unit->nama_unit }}</strong></td></tr>
                    <tr><td style="color: #666;">Harga</td><td>Rp {{ number_format($pengajuan->unit->harga_sewa, 0, ',', '.') }}/bln</td></tr>
                    <tr><td style="color: #666;">Durasi</td><td>{{ $pengajuan->durasi_sewa }} Bulan</td></tr>
                    <tr><td style="color: #666;">Mulai</td><td>{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Dokumen Persyaratan --}}
<div class="card">
    <div class="card-title"><i class="ri-file-list-3-line"></i> Dokumen Persyaratan</div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
        {{-- SESUAIKAN: Key di sini harus sama dengan 'jenis_dokumen' di database --}}
        @foreach(['ktp' => 'KTP', 'kk' => 'Kartu Keluarga', 'slip_gaji' => 'Slip Gaji'] as $key => $label)
            {{-- Kita filter koleksi dokumens berdasarkan jenis_dokumen --}}
            @php $doc = $pengajuan->dokumens->where('jenis_dokumen', $key)->first(); @endphp
            
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 12px; color: #666; margin-bottom: 10px;">{{ $label }}</div>
                @if($doc)
                    {{-- SESUAIKAN: Nama kolom di database lu adalah path_file, bukan file_path --}}
                    <a href="{{ Storage::url($doc->path_file) }}" target="_blank" class="btn btn-secondary" style="font-size: 12px; width: 100%; justify-content: center; display: flex; align-items: center; gap: 5px;">
                        <i class="ri-eye-line"></i> Lihat File
                    </a>
                    <div style="font-size: 10px; color: #22c55e; margin-top: 5px;">
                        <i class="ri-check-line"></i> Terupload
                    </div>
                @else
                    <div style="color: #dc2626; font-size: 12px; padding: 8px; background: #fef2f2; border-radius: 4px;">
                        <i class="ri-error-warning-line"></i> Belum Upload
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

    {{-- Kolom Kanan: Aksi Admin --}}
    <div>
        {{-- Update Status Biasa --}}
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-title">Aksi Cepat</div>
            <form action="{{ route('admin.pengajuan.update-status', $pengajuan->id) }}" method="POST">
                @csrf @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Ubah Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" @selected($pengajuan->status == 'pending')>Pending</option>
                        <option value="verifikasi_dokumen" @selected($pengajuan->status == 'verifikasi_dokumen')>Verifikasi Dokumen</option>
                        <option value="jadwal_survei" @selected($pengajuan->status == 'jadwal_survei')>Jadwal Survei</option>
                        <option value="dibatalkan" @selected($pengajuan->status == 'dibatalkan')>Batalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Admin</label>
                    <textarea name="catatan_admin" class="form-control" rows="3">{{ $pengajuan->catatan_admin }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update Status</button>
            </form>
        </div>

        {{-- Tombol Terima (Final) --}}
        @if($pengajuan->status != 'diterima' && $pengajuan->status != 'ditolak')
        <div class="card" style="border: 1px solid var(--green-200); background: var(--green-50);">
            <div class="card-title" style="color: var(--green-800);">Keputusan Final</div>
            
            {{-- Form Terima --}}
            <form action="{{ route('admin.pengajuan.terima', $pengajuan->id) }}" method="POST" style="margin-bottom: 15px;">
                @csrf
                <input type="hidden" name="tanggal_mulai" value="{{ $pengajuan->tanggal_mulai }}">
                <input type="hidden" name="harga_per_bulan" value="{{ $pengajuan->unit->harga_sewa }}">
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--green-600);" onclick="return confirm('Terima pengajuan ini? User akan otomatis menjadi penghuni dan tagihan akan dibuat.')">
                    <i class="ri-checkbox-circle-line"></i> TERIMA PENGAJUAN
                </button>
            </form>

            {{-- Button Tolak (Trigger Modal/Collapse) --}}
            <button class="btn btn-danger" style="width: 100%; justify-content: center;" onclick="toggleTolak()">
                <i class="ri-close-circle-line"></i> TOLAK PENGAJUAN
            </button>

            <div id="form-tolak" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #fca5a5;">
                <form action="{{ route('admin.pengajuan.tolak', $pengajuan->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" style="color: #b91c1c;">Alasan Penolakan</label>
                        <textarea name="catatan_admin" class="form-control" required placeholder="Sebutkan alasan penolakan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width: 100%; background: #dc2626;">Konfirmasi Tolak</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleTolak() {
        const form = document.getElementById('form-tolak');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endpush
@endsection