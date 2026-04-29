@extends('layouts.app')

@section('title', 'Ajukan Sewa')
@section('page-title', 'Formulir Pengajuan Sewa')

@section('content')
<div style="max-width:720px; margin: 0 auto;">

    {{-- Progress Stepper --}}
    <div style="display:flex; justify-content: space-between; margin-bottom: 30px; position: relative; padding: 0 50px;">
        <div id="step-1-indicator" style="text-align: center; z-index: 2;">
            <div id="circle-1" style="width: 35px; height: 35px; background: var(--green-600); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold; border: 3px solid white; box-shadow: 0 0 0 1px var(--green-600);">1</div>
            <span style="font-size: 12px; font-weight: 700; color: var(--green-900);">Informasi Sewa</span>
        </div>
        <div id="step-2-indicator" style="text-align: center; z-index: 2; opacity: 0.4;">
            <div id="circle-2" style="width: 35px; height: 35px; background: white; color: var(--green-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold; border: 3px solid white; box-shadow: 0 0 0 1px var(--green-200);">2</div>
            <span style="font-size: 12px; font-weight: 600; color: var(--green-700);">Upload Dokumen</span>
        </div>
        <div style="position: absolute; top: 18px; left: 80px; right: 80px; height: 2px; background: var(--green-100); z-index: 1;">
            <div id="progress-bar" style="width: 0%; height: 100%; background: var(--green-600); transition: 0.3s ease;"></div>
        </div>
    </div>

    <form action="{{ route('calon.pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="multiStepForm">
        @csrf
        <input type="hidden" name="unit_id" value="{{ $unit->id }}">

        {{-- STEP 1: INFORMASI SEWA & DATA DIRI --}}
        <div id="section-1">
            {{-- Info Unit Singkat --}}
            <div class="card" style="margin-bottom:20px; background: var(--green-50); border: 1px dashed var(--green-300);">
                <div style="display:flex; gap:16px; align-items:center">
                    <div style="width:48px; height:48px; background:white; border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--green-600); font-size:24px;">
                        <i class="ri-community-line"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:var(--green-900); font-size:16px;">{{ $unit->nama_unit }}</div>
                        <div style="font-size:13px; color:var(--green-700); font-weight:600;">
                            Rp {{ number_format($unit->harga_sewa,0,',','.') }} <span style="font-weight:400; opacity:0.8;">/ bulan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-title"><i class="ri-user-settings-line"></i> Data Pemohon</div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama (Sesuai Akun)</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly style="background: var(--cream-50); color: #666; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly style="background: var(--cream-50); color: #666; cursor: not-allowed;">
                    </div>
                </div>

                <div class="grid grid-2" style="margin-top:10px;">
                    <div class="form-group">
                        <label class="form-label">NIK <span style="color:red">*</span></label>
                        <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" required value="{{ old('nik') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP/WhatsApp <span style="color:red">*</span></label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxx" required value="{{ old('no_hp') }}">
                    </div>
                </div>

                <div class="card-title" style="margin-top:20px"><i class="ri-calendar-check-line"></i> Detail Rencana Sewa</div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Durasi Sewa</label>
                        <select name="durasi_sewa" id="durasi_sewa" class="form-control" required onchange="hitungTotal(this.value)">
                            <option value="">Pilih Durasi</option>
                            @foreach([1,3,6,12] as $d)
                                <option value="{{ $d }}" @selected(old('durasi_sewa') == $d)>{{ $d }} Bulan</option>
                            @endforeach
                        </select>
                        <div id="totalHarga" style="margin-top:8px; font-size:13px; color:var(--green-700); font-weight:700"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rencana Mulai Sewa</label>
                        <input type="date" name="tanggal_mulai" class="form-control" min="{{ now()->format('Y-m-d') }}" required value="{{ old('tanggal_mulai') }}">
                    </div>
                </div>

                <div style="margin-top:20px; border-top: 1px solid var(--green-100); padding-top:20px; text-align:right">
                    <button type="button" class="btn btn-primary" onclick="nextStep()" style="padding: 10px 25px">
                        Lanjut ke Dokumen <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- STEP 2: UPLOAD DOKUMEN --}}
        <div id="section-2" style="display:none;">
            <div class="card">
                <div class="card-title"><i class="ri-file-upload-line"></i> Upload Dokumen Pendukung</div>
                <p style="font-size:13px; color:#666; margin-bottom:20px">Format yang didukung: JPG, PNG, atau PDF (Maksimal 2MB per file).</p>

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">Foto KTP <span style="color:red">*</span></label>
                    <input type="file" name="file_ktp" class="form-control" required accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">Foto Kartu Keluarga <span style="color:red">*</span></label>
                    <input type="file" name="file_kk" class="form-control" required accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="form-group" style="margin-bottom:15px">
                    <label class="form-label">Slip Gaji / Bukti Penghasilan <span style="color:red">*</span></label>
                    <input type="file" name="file_slip_gaji" class="form-control" required accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div style="margin-top:30px; border-top: 1px solid var(--green-100); padding-top:20px; display:flex; justify-content: space-between;">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </button>
                    <button type="submit" class="btn btn-primary" style="background:var(--green-700)">
                        Kirim Pengajuan Sewa <i class="ri-send-plane-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const hargaPerBulan = {{ $unit->harga_sewa }};

    function hitungTotal(durasi) {
        const el = document.getElementById('totalHarga');
        if (!durasi) { el.textContent = ''; return; }
        const total = hargaPerBulan * parseInt(durasi);
        el.textContent = 'Estimasi Total: Rp ' + total.toLocaleString('id-ID');
    }

    function nextStep() {
        const section1 = document.getElementById('section-1');
        const section2 = document.getElementById('section-2');
        const bar = document.getElementById('progress-bar');
        const ind2 = document.getElementById('step-2-indicator');
        const circ2 = document.getElementById('circle-2');

        // Validasi Step 1
        const requireds = section1.querySelectorAll('[required]');
        let valid = true;
        requireds.forEach(input => {
            if(!input.value) {
                valid = false;
                input.style.borderColor = '#ef4444';
            } else {
                input.style.borderColor = '#d1ddd3';
            }
        });

        if(valid) {
            section1.style.display = 'none';
            section2.style.display = 'block';
            bar.style.width = '100%';
            ind2.style.opacity = '1';
            circ2.style.background = 'var(--green-600)';
            circ2.style.color = 'white';
            window.scrollTo(0, 0);
        } else {
            alert('Harap isi semua field yang berbintang (*) sebelum lanjut.');
        }
    }

    function prevStep() {
        const section1 = document.getElementById('section-1');
        const section2 = document.getElementById('section-2');
        const bar = document.getElementById('progress-bar');
        const ind2 = document.getElementById('step-2-indicator');
        const circ2 = document.getElementById('circle-2');

        section1.style.display = 'block';
        section2.style.display = 'none';
        bar.style.width = '0%';
        ind2.style.opacity = '0.4';
        circ2.style.background = 'white';
        circ2.style.color = 'var(--green-600)';
        window.scrollTo(0, 0);
    }
</script>
@endpush
@endsection