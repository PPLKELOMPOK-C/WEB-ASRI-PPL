@extends('layouts.app')
@section('title', 'Manajemen Unit')
@section('page-title', 'Daftar Unit Properti')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.unit.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Unit Baru
        </a>

        {{-- Form Filter --}}
        <form action="{{ route('admin.unit.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" placeholder="Cari blok/no kamar..." value="{{ request('search') }}" style="width: 200px;">
            
            <select name="wilayah" class="form-control" style="width: 150px;">
                <option value="">Semua Wilayah</option>
                @foreach(['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat'] as $w)
                    <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ $w }}</option>
                @endforeach
            </select>

            <select name="status" class="form-control" style="width: 130px;">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dihuni" {{ request('status') == 'dihuni' ? 'selected' : '' }}>Dihuni</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>

            <button type="submit" class="btn btn-secondary"><i class="ri-search-line"></i> Filter</button>
            @if(request()->anyFilled(['search', 'wilayah', 'status']))
                <a href="{{ route('admin.unit.index') }}" class="btn btn-light" title="Reset Filter"><i class="ri-refresh-line"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Info Unit</th>
                    <th>Wilayah</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td style="width: 80px;">
                        @if($unit->gambar)
                            <img src="{{ Storage::url($unit->gambar) }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 60px; height: 45px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                                <i class="ri-image-line"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--green-900)">{{ $unit->nama_unit ?? "Blok $unit->blok No. $unit->no_kamar" }}</div>
                        <div style="font-size: 12px; color: #666;">Lantai {{ $unit->lantai }} · {{ $unit->gedung }}</div>
                    </td>
                    <td><span style="font-size: 13px;">{{ $unit->wilayah }}</span></td>
                    <td><span style="font-weight: 600; color: var(--green-700)">Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }}</span></td>
                    <td>
                        @php
                            $badgeClass = [
                                'tersedia' => 'badge-success',
                                'dihuni' => 'badge-info',
                                'maintenance' => 'badge-danger'
                            ][$unit->status] ?? 'badge-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($unit->status) }}</span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('admin.unit.show', $unit) }}" class="btn btn-sm btn-light" title="Detail"><i class="ri-eye-line"></i></a>
                            <a href="{{ route('admin.unit.edit', $unit) }}" class="btn btn-sm btn-light" style="color: blue;" title="Edit"><i class="ri-edit-line"></i></a>
                            <form action="{{ route('admin.unit.destroy', $unit) }}" method="POST" onsubmit="return confirm('Hapus unit ini?')" style="display: inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light" style="color: red;" title="Hapus"><i class="ri-delete-bin-line"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                        <i class="ri-inbox-line" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                        Tidak ada data unit yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 20px;">
        {{ $units->links() }}
    </div>
</div>
@endsection