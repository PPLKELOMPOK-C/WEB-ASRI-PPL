@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ri-building-line"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_unit'] ?? 0 }}</div>
            <div class="stat-label">Total Unit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><i class="ri-home-smile-line"></i></div>
        <div>
            <div class="stat-value">{{ $stats['unit_tersedia'] ?? 0 }}</div>
            <div class="stat-label">Unit Tersedia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cream"><i class="ri-team-line"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_penghuni'] ?? 0 }}</div>
            <div class="stat-label">Total Penghuni</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-money-dollar-circle-line"></i></div>
        <div>
            {{-- Konversi ke Juta agar tampilan rapi --}}
            @php $pendapatan = ($stats['pendapatan_bulan_ini'] ?? 0) / 1000000; @endphp
            <div class="stat-value">Rp {{ number_format($pendapatan, 1) }}jt</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>
    </div>
</div>

{{-- Second Row --}}
<div class="grid grid-3" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-file-list-3-line"></i></div>
        <div>
            <div class="stat-value">{{ $stats['pengajuan_pending'] ?? 0 }}</div>
            <div class="stat-label">Pengajuan Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-bill-line"></i></div>
        <div>
            {{-- Bagian yang tadinya Error --}}
            <div class="stat-value">{{ $stats['tagihan_belum_bayar'] ?? 0 }}</div>
            <div class="stat-label">Tagihan Belum Lunas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-tools-line"></i></div>
        <div>
            <div class="stat-value">{{ $stats['laporan_open'] ?? 0 }}</div>
            <div class="stat-label">Laporan Kerusakan Open</div>
        </div>
    </div>
</div>

{{-- Tables --}}
<div class="grid grid-2">
    {{-- Pengajuan Terbaru --}}
    <div class="card">
        <div class="card-title">
            <i class="ri-file-list-3-line" style="color:var(--green-500)"></i>
            Pengajuan Terbaru
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanTerbaru as $p)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $p->user->name ?? 'User Tak Dikenal' }}</div>
                            <div style="font-size:12px;color:#5a7a5a">{{ $p->created_at->diffForHumans() }}</div>
                        </td>
                        <td>{{ $p->unit->blok ?? '-' }}{{ $p->unit->no_kamar ?? '-' }}</td>
                        <td><span class="badge badge-warning">{{ $p->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#5a7a5a;padding:20px">Tidak ada pengajuan baru</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Laporan Kerusakan --}}
    <div class="card">
        <div class="card-title">
            <i class="ri-tools-line" style="color:var(--green-500)"></i>
            Laporan Kerusakan Terbaru
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Penghuni</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanTerbaru as $l)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $l->user->name ?? 'Anonim' }}</div>
                            <div style="font-size:12px;color:#5a7a5a">{{ Str::limit($l->judul, 20) }}</div>
                        </td>
                        <td>{{ ucfirst($l->kategori) }}</td>
                        <td><span class="badge badge-danger">{{ $l->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.laporan.show', $l->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#5a7a5a;padding:20px">Tidak ada laporan open</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection