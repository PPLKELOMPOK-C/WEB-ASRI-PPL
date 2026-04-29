@extends('layouts.app')  
@section('title', 'Data Penghuni') 
@section('page-title', 'Manajemen Penghuni')  

@section('content') 
<div class="card">     
    <div class="card-title">         
        <i class="ri-group-line"></i> Daftar Penghuni Aktif     
    </div>      

    <div class="table-wrap">         
        <table>             
            <thead>                 
                <tr>                     
                    <th>Nama Penghuni</th>                     
                    <th>Kontak</th>                     
                    <th>NIK</th>                     
                    <th style="text-align: center;">Aksi</th>                 
                </tr>             
            </thead>             
            <tbody>                 
                @forelse($penghuni as $p)                                 
                <tr>                     
                    <td>                         
                        <div style="display: flex; align-items: center; gap: 12px;">                             
                            <div style="width: 35px; height: 35px; background: var(--green-100); color: var(--green-700); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold;">                                 
                                {{ strtoupper(substr($p->name, 0, 1)) }}                             
                            </div>                             
                            <div style="font-weight: 600;">{{ $p->name }}</div>                         
                        </div>                     
                    </td>                     

                    {{-- ✅ SUDAH DITAMBAHKAN --}}
                    <td>
                        <div style="font-size: 13px;">{{ $p->email }}</div>
                        <small style="color: #666;">
                            {{-- Ambil no_hp dari users, fallback ke pengajuan terbaru --}}
                            {{ $p->no_hp ?? ($p->pengajuanSewas->first()->no_hp ?? '-') }}
                        </small>
                    </td>

                    <td>
                        <code>
                            {{-- Ambil nik dari users, fallback ke pengajuan terbaru --}}
                            {{ $p->nik ?? ($p->pengajuanSewas->first()->nik ?? '-') }}
                        </code>
                    </td>

                    <td style="text-align: center;">                         
                        <a href="{{ route('admin.penghuni.show', $p->id) }}" class="btn btn-primary btn-sm">                             
                            <i class="ri-folder-open-line"></i> Buka Berkas                         
                        </a>                     
                    </td>                 
                </tr>                 

                @empty                 
                <tr>                     
                    <td colspan="4" style="text-align: center; padding: 30px; color: #999;">Belum ada data penghuni.</td>                 
                </tr>                 
                @endforelse             
            </tbody>         
        </table>     
    </div>      

    <div style="margin-top: 15px;">         
        {{ $penghuni->links() }}     
    </div> 
</div> 
@endsection