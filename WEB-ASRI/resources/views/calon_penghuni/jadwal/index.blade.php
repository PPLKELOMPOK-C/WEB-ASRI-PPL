```blade
@extends('layouts.app')
@section('title','Pilih Jadwal Survei')
@section('page-title','Pilih Jadwal Survei')

@section('content')
<div style="max-width:700px">

    <div style="background:var(--green-700);color:white;border-radius:12px;padding:16px 20px;margin-bottom:24px">
        <div style="font-size:12px;opacity:0.75;font-weight:600;text-transform:uppercase;letter-spacing:0.8px">Survei Unit</div>
        <div style="font-size:16px;font-weight:700;margin-top:2px">{{ $pengajuan->unit->nama_unit }}</div>
        <div style="font-size:13px;opacity:0.75;margin-top:2px">{{ $pengajuan->unit->gedung }} · {{ $pengajuan->unit->wilayah }}</div>
    </div>

    @if($jadwalAktif)
    <div class="alert alert-info" style="margin-bottom:20px">
        <i class="ri-calendar-check-line"></i>
        <div>
            <strong>Jadwal sudah dipilih:</strong>
            {{ \Carbon\Carbon::parse($jadwalAktif->tanggal_survei)->format('l, d M Y - H:i') }} ·
            Status: <strong>{{ ucfirst($jadwalAktif->status) }}</strong>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-title"><i class="ri-calendar-event-line" style="color:var(--green-500)"></i> Slot Waktu Tersedia</div>
        <p style="font-size:13px;color:#5a7a5a;margin-bottom:16px">Pilih waktu survei yang sesuai dengan jadwal Anda. Survei berlangsung hari kerja (Senin–Jumat).</p>

        <form method="POST" action="{{ route('calon.jadwal.pilih', $pengajuan->id) }}">
            @csrf

            {{-- Grouping slot per tanggal --}}
            @php
            $slotsByDate = collect($slotsAvailable)->groupBy('date');
            @endphp

            @if($slotsByDate->isEmpty())
            <div style="text-align:center;padding:24px;color:#5a7a5a">
                <p>Tidak ada slot tersedia dalam 14 hari ke depan. Silakan hubungi admin.</p>
            </div>
            @else
            <div style="display:grid;gap:16px;margin-bottom:20px">
            @foreach($slotsByDate as $date => $slots)
            <div style="border:1px solid #e8f0eb;border-radius:10px;overflow:hidden">
                <div style="background:var(--green-50);padding:10px 16px;font-size:13px;font-weight:700;color:var(--green-800)">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </div>
                <div style="padding:12px;display:flex;flex-wrap:wrap;gap:8px">
                @foreach($slots as $slot)
                <label style="cursor:pointer">
                    <input type="radio" name="tanggal_survei" value="{{ $slot['datetime'] }}" style="position:absolute;opacity:0;width:0;height:0" class="slot-radio">
                    <div class="slot-btn" style="padding:8px 16px;border-radius:8px;border:1.5px solid var(--green-200);font-size:13px;font-weight:600;color:var(--green-700);transition:all 0.15s;background:white">
                        {{ $slot['time'] }}
                    </div>
                </label>
                @endforeach
                </div>
            </div>
            @endforeach
            </div>

            @error('tanggal_survei')<div style="color:#e53e3e;font-size:12px;margin-bottom:12px">{{ $message }}</div>@enderror

            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary"><i class="ri-calendar-check-line"></i> Konfirmasi Jadwal</button>
                <a href="{{ route('calon.pengajuan.show', $pengajuan->id) }}" class="btn btn-secondary">Kembali</a>
            </div>
            @endif
        </form>
    </div>
</div>

@push('styles')
<style>
.slot-radio:checked + .slot-btn {
    background: var(--green-700) !important;
    color: white !important;
    border-color: var(--green-700) !important;
}
.slot-btn:hover { background: var(--green-50) !important; }
</style>
@endpush
@endsection
