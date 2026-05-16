@extends('layouts.app')

@section('title', 'Manajemen Tagihan')
@section('page-title', 'Daftar Tagihan Penghuni')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-4">
        <div class="card" style="border-left: 4px solid var(--green-600);">
            <div style="font-size: 13px; color: #666;">Total Tagihan</div>
            <div style="font-size: 20px; font-weight: 700; color: var(--green-900);">{{ $totalTagihan ?? 0 }}</div>
        </div>
        <div class="card" style="border-left: 4px solid #eab308;">
            <div style="font-size: 13px; color: #666;">Menunggu Verifikasi</div>
            <div style="font-size: 20px; font-weight: 700; color: #a16207;">{{ $menungguVerifikasi ?? 0 }}</div>
        </div>
        <div class="card" style="border-left: 4px solid #ef4444;">
            <div style="font-size: 13px; color: #666;">Total Piutang (Belum Bayar)</div>
            <div style="font-size: 20px; font-weight: 700; color: #b91c1c;">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card" style="border-left: 4px solid var(--green-500);">
            <div style="font-size: 13px; color: #666;">Pendapatan Bulan Ini</div>
            <div style="font-size: 20px; font-weight: 700; color: var(--green-700);">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="card">
        <form action="{{ route('admin.tagihan.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label class="form-label">Cari Penghuni / Unit</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau nomor unit..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="width: 180px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <i class="ri-filter-3-line"></i> Filter
            </button>
            <a href="{{ route('admin.tagihan.index') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">Reset</a>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: var(--green-50); border-bottom: 2px solid var(--green-100);">
                <tr>
                    <th style="padding: 15px;">Penghuni</th>
                    <th style="padding: 15px;">Unit</th>
                    <th style="padding: 15px;">Periode</th>
                    <th style="padding: 15px;">Jumlah</th>
                    <th style="padding: 15px;">Status</th>
                    <th style="padding: 15px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihans as $t)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">
                        <div style="font-weight: 600; color: var(--green-900);">{{ $t->user->name }}</div>
                        <div style="font-size: 12px; color: #666;">{{ $t->user->email }}</div>
                    </td>
                    <td style="padding: 15px;">{{ $t->unit->nama_unit }}</td>
                    <td style="padding: 15px;">{{ \Carbon\Carbon::parse($t->periode)->format('F Y') }}</td>
                    <td style="padding: 15px; font-weight: 600;">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                    <td style="padding: 15px;">
                        @if($t->status == 'lunas')
                            <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Lunas</span>
                        @elseif($t->status == 'menunggu_verifikasi')
                            <span style="background: #fef9c3; color: #854d0e; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Perlu Verifikasi</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Belum Bayar</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="{{ route('admin.tagihan.show', $t->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">
                            <i class="ri-eye-line"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #666;">
                        <i class="ri-file-search-line" style="font-size: 48px; opacity: 0.3;"></i>
                        <p style="margin-top: 10px;">Data tagihan tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 10px;">
        {{ $tagihans->links() }}
    </div>
</div>
@endsection