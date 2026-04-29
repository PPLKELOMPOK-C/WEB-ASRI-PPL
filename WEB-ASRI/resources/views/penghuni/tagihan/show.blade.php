@extends('layouts.app')
@section('title', 'Detail Tagihan')
@section('page-title', 'Detail Tagihan')

@section('content')
<div style="max-width:640px; margin: 0 auto;">
    <div class="card" style="border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 12px; padding: 24px; background: #fff;">
        
        {{-- Header: Status & Judul --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px">
            <div>
                <div style="font-size:24px; font-weight:700; color:#1a4d2e">Tagihan {{ $tagihan->periode }}</div>
                <div style="font-size:14px; color:#5a7a5a; margin-top:4px">{{ $tagihan->unit->nama_unit }}</div>
            </div>
            @if($tagihan->status === 'lunas')
                <span class="badge" style="background:#dcfce7; color:#166534; padding:8px 16px; border-radius:20px; font-weight:600;">✓ Lunas</span>
            @elseif($tagihan->status === 'menunggu_verifikasi')
                <span class="badge" style="background:#fef9c3; color:#854d0e; padding:8px 16px; border-radius:20px; font-weight:600;">Proses Pembayaran</span>
            @else
                <span class="badge" style="background:#fee2e2; color:#991b1b; padding:8px 16px; border-radius:20px; font-weight:600;">Belum Bayar</span>
            @endif
        </div>

        {{-- Box Informasi Jumlah --}}
        <div style="background:#f0f7f2; border-radius:12px; padding:20px; margin-bottom:24px; border: 1px solid #e1eee5;">
            <div style="font-size:13px; color:#5a7a5a; margin-bottom:4px">Total Tagihan</div>
            <div style="font-size:32px; font-weight:800; color:#1a4d2e; margin-bottom:8px">
                Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
            </div>
            <div style="font-size:13px; color:#5a7a5a; border-top: 1px solid #d1e2d7; padding-top: 8px; margin-top: 8px;">
                Jatuh tempo: <strong style="color:#2d3748">{{ $tagihan->jatuh_tempo->format('d M Y') }}</strong>
                @if($tagihan->jatuh_tempo->isPast() && $tagihan->status === 'belum_bayar')
                    <span style="color:#e53e3e; font-weight:700;"> (Terlambat {{ $tagihan->jatuh_tempo->diffInDays(now()) }} Hari)</span>
                @endif
            </div>
        </div>

        {{-- Area Pembayaran --}}
        <div style="margin-top:24px;">
            @if($tagihan->status === 'belum_bayar')
                <div style="text-align:center; padding: 20px; border: 1px dashed #cbd5e0; border-radius: 12px;">
                    <div style="font-weight:700; color:#2d3748; margin-bottom:8px;">Metode Pembayaran Online</div>
                    <p style="font-size:13px; color:#718096; margin-bottom:20px;">Gunakan QRIS, Virtual Account, atau E-Wallet untuk verifikasi otomatis.</p>

                    @if($snapToken)
                        <button id="pay-button" class="btn btn-primary" style="width:100%; padding:14px; font-size:16px; font-weight:700; border-radius:8px; cursor:pointer;">
                            <i class="ri-wallet-3-line"></i> Bayar Sekarang
                        </button>
                    @else
                        {{-- Tampilan jika SnapToken gagal terbit --}}
                        <div style="padding:16px; background:#fff5f5; border:1px solid #feb2b2; border-radius:8px; color:#c53030; text-align:left;">
                            <div style="font-weight:700; margin-bottom:4px;">⚠️ Gagal Menghubungkan ke Midtrans</div>
                            <div style="font-size:12px; line-height:1.5;">
                                <strong>Pesan Error:</strong> {{ $errorMessage ?? 'Token tidak ditemukan. Pastikan Email User sudah diisi di database dan Server Key benar.' }}
                            </div>
                        </div>
                    @endif
                </div>

            @elseif($tagihan->status === 'menunggu_verifikasi')
                <div style="text-align:center; padding:24px; background:#fffbeb; border:1px solid #fef3c7; border-radius:12px;">
                    <div style="font-weight:700; color:#92400e;">Transaksi Sedang Diproses</div>
                    <p style="font-size:13px; color:#b45309; margin-top:4px;">Silakan selesaikan pembayaran di aplikasi bank/e-wallet Anda.</p>
                    <button onclick="window.location.reload()" class="btn btn-sm" style="margin-top:12px; background:#f59e0b; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Cek Status Sekarang</button>
                </div>

            @elseif($tagihan->status === 'lunas')
                <div style="text-align:center; padding:24px; background:#f0fdf4; border:1px solid #dcfce7; border-radius:12px;">
                    <div style="color:#15803d; font-weight:700; font-size:18px;">Pembayaran Berhasil</div>
                    <p style="font-size:13px; color:#166534; margin-top:4px;">Terima kasih! Pembayaran telah diverifikasi pada {{ $tagihan->tanggal_bayar->format('d/m/Y H:i') }}</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="margin-top:32px; border-top:1px solid #edf2f7; padding-top:16px; text-align:center;">
            <a href="{{ route('penghuni.tagihan.index') }}" style="text-decoration:none; color:#4a5568; font-size:14px; font-weight:600;">
                ← Kembali ke Daftar Tagihan
            </a>
        </div>
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
{{-- Load Midtrans Snap JS --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    if(payButton) {
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    alert("Pembayaran Berhasil!");
                    window.location.href = "{{ route('penghuni.tagihan.index') }}";
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    alert("Gagal memproses pembayaran.");
                    console.error(result);
                },
                onClose: function() {
                    console.log('User menutup popup tanpa membayar.');
                }
            });
        });
    }
</script>
@endpush
@endsection