@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    
    <div class="card" style="background: var(--green-50); border: 1px solid var(--green-200);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="color: var(--green-900);">ID Pengajuan: #{{ $pengajuan->id }}</h3>
                <p>Unit: <strong>{{ $pengajuan->unit->nama_unit }}</strong> | Status: <strong>{{ strtoupper($pengajuan->status) }}</strong></p>
            </div>
            <i class="ri-checkbox-circle-line" style="font-size: 40px; color: var(--green-600);"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-title"><i class="ri-file-list-3-line"></i> Dokumen Terkirim</div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; text-align: left;">
                    <th style="padding: 12px;">Jenis Dokumen</th>
                    <th style="padding: 12px;">Nama File</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tipe = ['ktp' => 'KTP', 'kk' => 'Kartu Keluarga', 'slip_gaji' => 'Slip Gaji'];
                @endphp
                @foreach($tipe as $key => $label)
                    @php $file = $dokumens->where('jenis_dokumen', $key)->first(); @endphp
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;">{{ $label }}</td>
                        <td style="padding: 12px; color: #666;">{{ $file ? $file->nama_file : 'Not Found' }}</td>
                        <td style="padding: 12px; text-align: center;">
                            @if($file)
                                <a href="{{ asset('storage/' . $file->path_file) }}" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Lihat</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection