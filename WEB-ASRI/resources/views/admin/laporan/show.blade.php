@extends('layouts.app')

@section('title', 'Detail Laporan Kerusakan')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Detail Laporan Kerusakan</h4>
            <small class="text-muted">{{ $laporan->judul }}</small>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-success btn-sm px-4 fw-bold shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Info Laporan --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-semibold">
                    Informasi Laporan
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted fw-normal" style="width:160px">Judul</th>
                            <td>{{ $laporan->judul }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Kategori</th>
                            <td>
                                <span class="badge bg-info text-dark">{{ ucfirst($laporan->kategori) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Status</th>
                            <td>
                                @php
                                    $statusColor = match($laporan->status) {
                                        'open'        => 'danger',
                                        'in_progress' => 'warning',
                                        'resolved'    => 'success',
                                        'closed'      => 'secondary',
                                        default       => 'light',
                                    };
                                    $statusLabel = match($laporan->status) {
                                        'open'        => 'Open',
                                        'in_progress' => 'In Progress',
                                        'resolved'    => 'Resolved',
                                        'closed'      => 'Closed',
                                        default       => $laporan->status,
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Deskripsi</th>
                            <td>{{ $laporan->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Tanggal Lapor</th>
                            <td>{{ $laporan->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @if($laporan->resolved_at)
                        <tr>
                            <th class="text-muted fw-normal">Diselesaikan</th>
                            <td>{{ \Carbon\Carbon::parse($laporan->resolved_at)->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($laporan->respon_admin)
                        <tr>
                            <th class="text-muted fw-normal">Respon Admin</th>
                            <td>{{ $laporan->respon_admin }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Penghuni & Unit --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom fw-semibold">
                    Penghuni
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-semibold">{{ $laporan->user->name ?? '-' }}</p>
                    <p class="mb-1 text-muted small">{{ $laporan->user->email ?? '-' }}</p>
                    <p class="mb-0 text-muted small">{{ $laporan->user->phone ?? '-' }}</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-semibold">
                    Unit
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-semibold">{{ $laporan->unit->nama_unit ?? $laporan->unit->nomor_unit ?? '-' }}</p>
                    <p class="mb-0 text-muted small">{{ $laporan->unit->lantai ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Form Update Status --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom fw-semibold">
                    Update Status Laporan
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.laporan.status', $laporan->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="open"        {{ $laporan->status === 'open'        ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $laporan->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved"    {{ $laporan->status === 'resolved'    ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed"      {{ $laporan->status === 'closed'      ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Catatan / Respon Admin <span class="text-muted fw-normal">(opsional)</span></label>
                                <textarea name="respon_admin" class="form-control" rows="2"
                                    placeholder="Tulis catatan untuk penghuni..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection