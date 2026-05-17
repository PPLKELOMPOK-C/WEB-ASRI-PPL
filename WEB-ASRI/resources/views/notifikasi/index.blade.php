@extends('layouts.app')
@section('title','Notifikasi')
@section('page-title','Notifikasi')

@section('content')

<div style="max-width:680px">

    {{-- 1. Section Pengajuan Pending (khusus admin) --}}
    @php 
        $hasPending = !empty($pengajuanPending) && $pengajuanPending->count() > 0;
        $hasNotif = $notifikasis->count() > 0;
    @endphp

    @if($hasPending)
    <div style="margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <i class="ri-file-list-3-line" style="color:#b45309;font-size:18px"></i>
            <span style="font-size:14px;font-weight:700;color:#92400e">Pengajuan Menunggu Tindakan</span>
            <span style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;border:1px solid #fcd34d">
                {{ $pengajuanPending->count() }} pending
            </span>
        </div>

        <div style="display:grid;gap:8px">
        @foreach($pengajuanPending as $p)
        <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:14px 16px;display:flex;gap:12px;align-items:center">
            <div style="width:36px;height:36px;border-radius:50%;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ri-time-line" style="font-size:16px"></i>
            </div>
            <div style="flex:1">
                <div style="font-size:14px;font-weight:700;color:#78350f">{{ $p->user->name ?? '-' }}</div>
                <div style="font-size:12px;color:#92400e;margin-top:2px">
                    Unit: <strong>{{ $p->unit->nama_unit ?? '-' }}</strong>
                    &nbsp;·&nbsp; {{ $p->created_at->diffForHumans() }}
                </div>
            </div>
            <a href="{{ route('admin.pengajuan.show', $p->id) }}"
               style="font-size:12px;font-weight:700;color:#b45309;text-decoration:none;white-space:nowrap;border:1px solid #fcd34d;padding:4px 12px;border-radius:6px;background:white">
                Tinjau →
            </a>
        </div>
        @endforeach
        </div>
    </div>
    @endif

    {{-- 2. List Notifikasi Umum --}}
    @if($hasNotif || $hasPending)
        {{-- Header hanya muncul jika ada isi (baik pending maupun notif biasa) --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <div style="display:flex;gap:10px">
                @if($hasNotif)
                <form method="POST" action="{{ route('notifikasi.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="ri-check-double-line"></i> Tandai Semua Dibaca</button>
                </form>
                <form method="POST" action="{{ route('notifikasi.destroy-all') }}" onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i> Hapus Semua</button>
                </form>
                @endif
            </div>
        </div>

        <div style="display:grid;gap:10px">
        @foreach($notifikasis as $n)
        <div style="background:white;border:1px solid {{ !$n->is_read ? 'var(--green-200)' : '#e8f0eb' }};border-radius:10px;padding:14px 16px;display:flex;gap:12px;{{ !$n->is_read ? 'background:var(--green-50)' : '' }}">
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                background:{{ ['info'=>'#dbeafe','success'=>'#dcfce7','warning'=>'#fef9c3','danger'=>'#fee2e2'][$n->tipe]??'#f3f4f6' }};
                color:{{ ['info'=>'#1e40af','success'=>'#166534','warning'=>'#854d0e','danger'=>'#991b1b'][$n->tipe]??'#374151' }}">
                <i class="{{ ['info'=>'ri-information-line','success'=>'ri-checkbox-circle-line','warning'=>'ri-alert-line','danger'=>'ri-close-circle-line'][$n->tipe]??'ri-notification-3-line' }}" style="font-size:16px"></i>
            </div>

            <div style="flex:1">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px">
                    <div style="font-size:14px;font-weight:{{ !$n->is_read ? '700' : '600' }};color:var(--green-900)">{{ $n->judul }}</div>
                    <div style="font-size:11px;color:#5a7a5a;white-space:nowrap">{{ $n->created_at->diffForHumans() }}</div>
                </div>
                <div style="font-size:13px;color:#4a5a4a;margin-top:3px;line-height:1.6">{{ $n->pesan }}</div>
                <div style="display:flex;gap:10px;margin-top:8px;align-items:center">
                    @if($n->link)
                    <a href="{{ $n->link }}" style="font-size:12px;color:var(--green-600);font-weight:600;text-decoration:none">Lihat →</a>
                    @endif
                    @if(!$n->is_read)
                    <form method="POST" action="{{ route('notifikasi.read', $n->id) }}">
                        @csrf
                        <button type="submit" style="background:none;border:none;font-size:12px;color:#5a7a5a;cursor:pointer;padding:0">Tandai dibaca</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('notifikasi.destroy', $n->id) }}" onsubmit="return confirm('Hapus notifikasi?')" style="margin-left:auto">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#d1d5db;cursor:pointer;padding:0;font-size:14px"><i class="ri-close-line"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        </div>
        
        <div style="margin-top:16px">{{ $notifikasis->links() }}</div>

    @else
        {{-- 3. Empty State: Hanya muncul jika benar-benar tidak ada pending DAN tidak ada notif --}}
        <div style="text-align:center;padding:60px;color:#5a7a5a;background:white;border-radius:12px;border:1px solid #e8f0eb">
            <i class="ri-notification-off-line" style="font-size:48px;display:block;opacity:0.3;margin-bottom:12px"></i>
            <div style="font-size:16px;font-weight:600">Tidak ada notifikasi</div>
            <div style="font-size:13px;margin-top:6px">Anda akan menerima pemberitahuan di sini</div>
        </div>
    @endif
</div>
@endsection