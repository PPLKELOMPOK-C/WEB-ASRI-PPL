@extends('layouts.app')

@section('title', 'Laporan Kerusakan')

@section('content')
<div style="padding: 1.5rem; background: #f7f6f3; min-height: 100vh;">

    {{-- Page Header --}}
    <div style="margin-bottom: 1.5rem;">
        <h4 style="font-size: 20px; font-weight: 500; margin: 0; color: #1a1a18;">Kelola laporan kerusakan dari penghuni</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 1.5rem;">

        {{-- Open --}}
        <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #E24B4A; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Open</div>
            <div style="font-size: 28px; font-weight: 500; color: #E24B4A; line-height: 1;">{{ $summary['open'] }}</div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 4px;">perlu ditindaklanjuti</div>
        </div>

        {{-- In Progress --}}
        <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #EF9F27; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">In Progress</div>
            <div style="font-size: 28px; font-weight: 500; color: #EF9F27; line-height: 1;">{{ $summary['in_progress'] }}</div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 4px;">sedang dikerjakan</div>
        </div>

        {{-- Resolved --}}
        <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #639922; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Resolved</div>
            <div style="font-size: 28px; font-weight: 500; color: #639922; line-height: 1;">{{ $summary['resolved'] }}</div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 4px;">sudah diselesaikan</div>
        </div>

        {{-- Closed --}}
        <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #888780; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">Closed</div>
            <div style="font-size: 28px; font-weight: 500; color: #888780; line-height: 1;">{{ $summary['closed'] }}</div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 4px;">total ditutup</div>
        </div>

    </div>

    {{-- Filter --}}
    <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.laporan.index') }}">
            <div style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">

                <div style="display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 180px;">
                    <label style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em;">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama penghuni / judul..."
                        style="height: 34px; font-size: 13px; border: 0.5px solid rgba(0,0,0,0.2); border-radius: 8px; padding: 0 10px; background: #f7f6f3; color: #1a1a18; outline: none; width: 100%;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em;">Status</label>
                    <select name="status" style="height: 34px; font-size: 13px; border: 0.5px solid rgba(0,0,0,0.2); border-radius: 8px; padding: 0 10px; background: #f7f6f3; color: #1a1a18; width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="open"        {{ request('status') === 'open'        ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved"    {{ request('status') === 'resolved'    ? 'selected' : '' }}>Resolved</option>
                        <option value="closed"      {{ request('status') === 'closed'      ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em;">Kategori</label>
                    <select name="kategori" style="height: 34px; font-size: 13px; border: 0.5px solid rgba(0,0,0,0.2); border-radius: 8px; padding: 0 10px; background: #f7f6f3; color: #1a1a18; width: 150px;">
                        <option value="">Semua Kategori</option>
                        <option value="Listrik"  {{ request('kategori') === 'Listrik'  ? 'selected' : '' }}>Listrik</option>
                        <option value="Air"      {{ request('kategori') === 'Air'      ? 'selected' : '' }}>Air</option>
                        <option value="Bangunan" {{ request('kategori') === 'Bangunan' ? 'selected' : '' }}>Bangunan</option>
                        <option value="Lainnya"  {{ request('kategori') === 'Lainnya'  ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div style="display: flex; gap: 6px;">
                    <button type="submit" class="btn btn-primary" style="height: 42px;">
                        <i class="ri-filter-3-line"></i> Filter
                    </button>
                    <a href="{{ route('admin.laporan.index') }}"
                        style="height: 34px; padding: 0 14px; font-size: 13px; font-weight: 500; border-radius: 8px; border: 0.5px solid rgba(0,0,0,0.2); background: #f7f6f3; color: #6b6b67; cursor: pointer; display: inline-flex; align-items: center; text-decoration: none;">
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Table --}}
    <div style="background: #fff; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden;">

        {{-- Table Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 0.5px solid rgba(0,0,0,0.08);">
            <span style="font-size: 14px; font-weight: 500; color: #1a1a18;">Semua Laporan</span>
            <span style="font-size: 12px; color: #9b9b97;">
                Menampilkan {{ $laporans->firstItem() }}–{{ $laporans->lastItem() }} dari {{ $laporans->total() }} laporan
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f6f3;">
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08); width: 40px;">#</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Penghuni</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Unit</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Judul</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Kategori</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Status</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: left; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Tanggal</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 14px; text-align: center; border-bottom: 0.5px solid rgba(0,0,0,0.08);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $laporan)
                    @php
                        $initials = collect(explode(' ', $laporan->user->name ?? 'U'))
                            ->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');

                        $avatarColors = [
                            ['bg'=>'#EEEDFE','color'=>'#534AB7'],
                            ['bg'=>'#E1F5EE','color'=>'#0F6E56'],
                            ['bg'=>'#FAECE7','color'=>'#993C1D'],
                            ['bg'=>'#FBEAF0','color'=>'#993556'],
                            ['bg'=>'#E6F1FB','color'=>'#185FA5'],
                        ];
                        $avatarStyle = $avatarColors[$loop->index % count($avatarColors)];

                        $statusBadge = match($laporan->status) {
                            'open'        => ['bg'=>'#FCEBEB','color'=>'#A32D2D','label'=>'Open'],
                            'in_progress' => ['bg'=>'#FAEEDA','color'=>'#854F0B','label'=>'In Progress'],
                            'resolved'    => ['bg'=>'#EAF3DE','color'=>'#3B6D11','label'=>'Resolved'],
                            'closed'      => ['bg'=>'#F1EFE8','color'=>'#5F5E5A','label'=>'Closed'],
                            default       => ['bg'=>'#F1EFE8','color'=>'#5F5E5A','label'=>ucfirst($laporan->status)],
                        };
                    @endphp
                    <tr style="border-bottom: 0.5px solid rgba(0,0,0,0.06);">
                        <td style="padding: 12px 14px; font-size: 12px; color: #9b9b97;">
                            {{ $laporans->firstItem() + $loop->index }}
                        </td>
                        <td style="padding: 12px 14px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $avatarStyle['bg'] }}; color: {{ $avatarStyle['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; flex-shrink: 0;">
                                    {{ $initials }}
                                </div>
                                <span style="font-size: 13px; color: #1a1a18;">{{ $laporan->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td style="padding: 12px 14px; font-size: 12px; color: #6b6b67;">
                            {{ $laporan->unit->nama_unit ?? $laporan->unit->nomor_unit ?? '-' }}
                        </td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #1a1a18;">
                            {{ $laporan->judul }}
                        </td>
                        <td style="padding: 12px 14px;">
                            <span style="display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; background: #E6F1FB; color: #185FA5;">
                                {{ $laporan->kategori }}
                            </span>
                        </td>
                        <td style="padding: 12px 14px;">
                            <span style="display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['color'] }};">
                                {{ $statusBadge['label'] }}
                            </span>
                        </td>
                        <td style="padding: 12px 14px; font-size: 12px; color: #6b6b67;">
                            {{ $laporan->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 12px 14px; text-align: center;">
                            <a href="{{ route('admin.laporan.show', $laporan->id) }}"
                                style="display: inline-block; font-size: 12px; padding: 5px 12px; border: 0.5px solid rgba(0,0,0,0.2); border-radius: 8px; background: transparent; color: #6b6b67; text-decoration: none; transition: all 0.15s;"
                                onmouseover="this.style.background='#E6F1FB';this.style.color='#185FA5';this.style.borderColor='#B5D4F4';"
                                onmouseout="this.style.background='transparent';this.style.color='#6b6b67';this.style.borderColor='rgba(0,0,0,0.2)';">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 3rem; text-align: center; color: #9b9b97; font-size: 13px;">
                            Tidak ada laporan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($laporans->hasPages())
        <div style="padding: 12px 16px; border-top: 0.5px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 12px; color: #9b9b97;">
                Halaman {{ $laporans->currentPage() }} dari {{ $laporans->lastPage() }}
            </span>
            {{ $laporans->links() }}
        </div>
        @endif

    </div>

</div>
@endsection