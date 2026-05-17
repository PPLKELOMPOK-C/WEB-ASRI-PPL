@extends('layouts.app')

@section('title', 'Daftar Pengajuan')
@section('page-title', 'Manajemen Pengajuan Sewa')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Statistik Ringkas (Status Counts) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        @php
            $statuses = [
                'pending' => ['label' => 'Menunggu', 'icon' => 'ri-time-line', 'color' => '#f59e0b'],
                'verifikasi_dokumen' => ['label' => 'Verifikasi', 'icon' => 'ri-file-search-line', 'color' => '#3b82f6'],
                'jadwal_survei' => ['label' => 'Survei', 'icon' => 'ri-map-pin-user-line', 'color' => '#8b5cf6'],
                'diterima' => ['label' => 'Diterima', 'icon' => 'ri-checkbox-circle-line', 'color' => 'var(--green-600)'],
            ];
        @endphp

        @foreach($statuses as $key => $val)
        <div class="card" style="padding: 15px; border-bottom: 3px solid {{ $val['color'] }};">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">{{ $val['label'] }}</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--green-900);">{{ $statusCounts[$key] ?? 0 }}</div>
                </div>
                <i class="{{ $val['icon'] }}" style="font-size: 32px; color: {{ $val['color'] }}; opacity: 0.3;"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <div class="card">
        <form action="{{ route('admin.pengajuan.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div class="form-group">
                <label class="form-label">Cari Nama / Email</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ketik nama penghuni...">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $val)
                        <option value="{{ $key }}" @selected(request('status') == $key)>{{ $val['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 15px;"><i class="ri-search-line"></i></button>
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary" style="padding: 10px 15px;"><i class="ri-refresh-line"></i></a>
            </div>
        </form>
    </div>

    {{-- Tabel Pengajuan --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead style="background: var(--green-50); border-bottom: 1px solid var(--green-100);">
                <tr>
                    <th style="padding: 15px; text-align: left; color: var(--green-900);">ID</th>
                    <th style="padding: 15px; text-align: left; color: var(--green-900);">Pemohon</th>
                    <th style="padding: 15px; text-align: left; color: var(--green-900);">Unit</th>
                    <th style="padding: 15px; text-align: center; color: var(--green-900);">Durasi</th>
                    <th style="padding: 15px; text-align: left; color: var(--green-900);">Status</th>
                    <th style="padding: 15px; text-align: left; color: var(--green-900);">Tgl Masuk</th>
                    <th style="padding: 15px; text-align: center; color: var(--green-900);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">#{{ $p->id }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 700; color: var(--green-900);">{{ $p->user->name }}</div>
                        <div style="font-size: 11px; color: #666;">{{ $p->user->email }}</div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="font-weight: 600;">{{ $p->unit->nama_unit }}</span>
                    </td>
                    <td style="padding: 15px; text-align: center;">{{ $p->durasi_sewa }} Bln</td>
                    <td style="padding: 15px;">
                        <span class="badge" style="
                            @if($p->status == 'pending') background: #fef3c7; color: #92400e;
                            @elseif($p->status == 'diterima') background: var(--green-100); color: var(--green-800);
                            @elseif($p->status == 'ditolak') background: #fee2e2; color: #991b1b;
                            @else background: #e0e7ff; color: #3730a3; @endif
                            padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
                        ">
                            {{ strtoupper(str_replace('_', ' ', $p->status)) }}
                        </span>
                    </td>
                    <td style="padding: 15px; font-size: 12px; color: #666;">
                        {{ $p->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            Detail <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #666;">
                        <i class="ri-inbox-line" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        Belum ada data pengajuan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Pagination --}}
        @if($pengajuans->hasPages())
        <div style="padding: 15px; background: #fcfcfc; border-top: 1px solid #eee;">
            {{ $pengajuans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection