@extends('layouts.app')

@section('title', 'Statistik Analitik')

@section('content')
<div style="padding: 1.5rem; background: #f7f6f3; min-height: 100vh; font-family: inherit;">

    {{-- Page Header --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <div style="font-size: 11px; font-weight: 500; color: #9b9b97; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px;">
                Laporan eksekutif
            </div>
            <h2 style="font-size: 20px; font-weight: 500; color: #1a1a18; margin: 0;">Analitik &amp; Statistik</h2>
        </div>
        <button onclick="window.print()"
            style="height: 32px; padding: 0 14px; font-size: 12px; font-weight: 500; border-radius: 8px; border: .5px solid rgba(0,0,0,0.2); background: #fff; color: #6b6b67; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="3" y="1" width="10" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                <rect x="3" y="10" width="10" height="5" rx="1" stroke="currentColor" stroke-width="1.2"/>
                <path d="M5 13h6M5 11.5h3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M3 9H1.5A.5.5 0 0 0 1 9.5v4a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-.5-.5H13" stroke="currentColor" stroke-width="1.2"/>
            </svg>
            Cetak laporan
        </button>
    </div>

    {{-- Metric Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 1.5rem;">

        {{-- Ocupancy --}}
        <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #1D9E75; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px;">Tingkat okupansi</div>
            <div style="font-size: 26px; font-weight: 500; color: #0F6E56; line-height: 1;">{{ $tingkatOkupansi }}%</div>
            <div style="height: 4px; background: rgba(0,0,0,0.08); border-radius: 4px; margin-top: 10px;">
                <div style="height: 4px; background: #1D9E75; border-radius: 4px; width: {{ $tingkatOkupansi }}%;"></div>
            </div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 4px;">dari total unit tersedia</div>
        </div>

        {{-- Revenue --}}
        <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #185FA5; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px;">Total pendapatan thn ini</div>
            <div style="font-size: 20px; font-weight: 500; color: #185FA5; line-height: 1;">
                Rp {{ number_format($pendapatanBulanan->sum('total'), 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 6px;">seluruh unit aktif</div>
        </div>

        {{-- Applications --}}
        <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #534AB7; border-radius: 12px 12px 0 0;"></div>
            <div style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px;">Total pengajuan masuk</div>
            <div style="font-size: 26px; font-weight: 500; color: #534AB7; line-height: 1;">{{ $pengajuanPerBulan->sum('total') }}</div>
            <div style="font-size: 11px; color: #9b9b97; margin-top: 6px;">tahun berjalan</div>
        </div>

    </div>

    {{-- Main content: chart + donut --}}
    <div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 12px; margin-bottom: 1.5rem;">

        {{-- Tren Chart --}}
        <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: .9rem 1.25rem; border-bottom: .5px solid rgba(0,0,0,0.08);">
                <span style="font-size: 13px; font-weight: 500; color: #1a1a18;">Tren keuangan &amp; aktivitas sewa</span>
                <div style="display: flex; gap: 14px;">
                    <span style="display: flex; align-items: center; gap: 5px; font-size: 11px; color: #6b6b67;">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: #1D9E75; display: inline-block;"></span>
                        Pendapatan
                    </span>
                    <span style="display: flex; align-items: center; gap: 5px; font-size: 11px; color: #6b6b67;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #534AB7; display: inline-block;"></span>
                        Pengajuan
                    </span>
                </div>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <div style="position: relative; width: 100%; height: 220px;">
                    <canvas id="chartUtama" role="img" aria-label="Grafik tren pendapatan dan pengajuan sewa per bulan">Grafik batang pendapatan dan garis pengajuan sewa bulanan.</canvas>
                </div>
            </div>
        </div>

        {{-- Donut + stats --}}
        <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden;">
            <div style="padding: .9rem 1.25rem; border-bottom: .5px solid rgba(0,0,0,0.08);">
                <span style="font-size: 13px; font-weight: 500; color: #1a1a18;">Komposisi pengajuan</span>
            </div>
            <div style="padding: 1rem 1.25rem; display: flex; flex-direction: column; align-items: center;">
                @php
                    $totalDiterima = $pengajuanPerBulan->sum('diterima');
                    $totalDitolak  = $pengajuanPerBulan->sum('ditolak');
                    $totalAll      = $totalDiterima + $totalDitolak;
                    $pctDiterima   = $totalAll > 0 ? round(($totalDiterima / $totalAll) * 100) : 0;
                @endphp
                <div style="position: relative; width: 100%; height: 160px; display: flex; justify-content: center;">
                    <canvas id="chartDonut" role="img" aria-label="Donut chart komposisi pengajuan diterima dan ditolak">Diterima {{ $totalDiterima }}, Ditolak {{ $totalDitolak }}.</canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; pointer-events: none;">
                        <div style="font-size: 22px; font-weight: 500; color: #1a1a18; line-height: 1;">{{ $pctDiterima }}%</div>
                        <div style="font-size: 10px; color: #9b9b97; margin-top: 2px;">diterima</div>
                    </div>
                </div>
                <div style="width: 100%; margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: .5px solid rgba(0,0,0,0.08);">
                        <span style="font-size: 12px; color: #6b6b67;">Diterima</span>
                        <span style="font-size: 13px; font-weight: 500; color: #3B6D11;">{{ $totalDiterima }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: .5px solid rgba(0,0,0,0.08);">
                        <span style="font-size: 12px; color: #6b6b67;">Ditolak</span>
                        <span style="font-size: 13px; font-weight: 500; color: #A32D2D;">{{ $totalDitolak }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                        <span style="font-size: 12px; color: #6b6b67;">Total</span>
                        <span style="font-size: 13px; font-weight: 500; color: #1a1a18;">{{ $totalAll }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel Okupansi Per Wilayah --}}
    <div style="background: #fff; border: .5px solid rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden;">
        <div style="padding: .9rem 1.25rem; border-bottom: .5px solid rgba(0,0,0,0.08);">
            <span style="font-size: 13px; font-weight: 500; color: #1a1a18;">Okupansi berdasarkan wilayah</span>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr style="background: #f7f6f3;">
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .04em; padding: 9px 14px; text-align: left; border-bottom: .5px solid rgba(0,0,0,0.08); width: 28%;">Wilayah</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .04em; padding: 9px 14px; text-align: center; border-bottom: .5px solid rgba(0,0,0,0.08); width: 16%;">Total unit</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .04em; padding: 9px 14px; text-align: center; border-bottom: .5px solid rgba(0,0,0,0.08); width: 16%;">Dihuni</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .04em; padding: 9px 14px; text-align: center; border-bottom: .5px solid rgba(0,0,0,0.08); width: 16%;">Tersedia</th>
                        <th style="font-size: 11px; font-weight: 500; color: #6b6b67; text-transform: uppercase; letter-spacing: .04em; padding: 9px 14px; text-align: left; border-bottom: .5px solid rgba(0,0,0,0.08); width: 24%;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($okupansiPerWilayah as $row)
                    @php
                        $pct = $row->total > 0 ? round(($row->dihuni / $row->total) * 100, 1) : 0;
                        $isLow = $pct < 70;
                        $badgeBg    = $isLow ? '#FAEEDA' : '#EAF3DE';
                        $badgeColor = $isLow ? '#854F0B' : '#3B6D11';
                        $barColor   = $isLow ? '#EF9F27' : '#1D9E75';
                    @endphp
                    <tr style="border-bottom: .5px solid rgba(0,0,0,0.06);">
                        <td style="padding: 11px 14px; font-size: 13px; font-weight: 500; color: #1a1a18;">{{ $row->wilayah }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; text-align: center; color: #6b6b67;">{{ $row->total }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; text-align: center; font-weight: 500; color: {{ $badgeColor }};">{{ $row->dihuni }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; text-align: center; color: #9b9b97;">{{ $row->tersedia }}</td>
                        <td style="padding: 11px 14px;">
                            <span style="display: inline-flex; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; background: {{ $badgeBg }}; color: {{ $badgeColor }};">{{ $pct }}%</span>
                            <div style="height: 4px; background: rgba(0,0,0,0.08); border-radius: 4px; margin-top: 5px;">
                                <div style="height: 4px; background: {{ $barColor }}; border-radius: 4px; width: {{ $pct }}%;"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    const labelBulan    = {!! json_encode($pendapatanBulanan->pluck('bulan')) !!};
    const dataPendapatan = {!! json_encode($pendapatanBulanan->pluck('total')) !!};
    const dataPengajuan  = {!! json_encode($pengajuanPerBulan->pluck('total')) !!};

    new Chart(document.getElementById('chartUtama'), {
        type: 'bar',
        data: {
            labels: labelBulan,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: dataPendapatan,
                    backgroundColor: 'rgba(29,158,117,0.15)',
                    borderColor: '#1D9E75',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Pengajuan',
                    data: dataPengajuan,
                    type: 'line',
                    borderColor: '#534AB7',
                    pointBackgroundColor: '#534AB7',
                    pointRadius: 3,
                    tension: 0.4,
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            if (ctx.datasetIndex === 0)
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                            return ' ' + ctx.raw + ' pengajuan';
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { font: { size: 10 }, color: '#9b9b97', autoSkip: false, maxRotation: 0 },
                    grid: { display: false }
                },
                y: {
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: v => {
                            if (v >= 1000000000) return 'Rp ' + Math.round(v / 1000000000) + 'M';
                            if (v >= 1000000)    return 'Rp ' + Math.round(v / 1000000) + 'jt';
                            if (v >= 1000)       return 'Rp ' + Math.round(v / 1000) + 'rb';
                            return 'Rp ' + Math.round(v);
                        },
                        font: { size: 10 },
                        color: '#9b9b97',
                        maxTicksLimit: 6
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                y1: {
                    position: 'right',
                    ticks: { font: { size: 10 }, color: '#9b9b97' },
                    grid: { display: false }
                }
            }
        }
    });

    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Diterima', 'Ditolak'],
            datasets: [{
                data: [{{ $pengajuanPerBulan->sum('diterima') }}, {{ $pengajuanPerBulan->sum('ditolak') }}],
                backgroundColor: ['#1D9E75', '#E24B4A'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '78%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.raw }
                }
            }
        }
    });
</script>
@endpush