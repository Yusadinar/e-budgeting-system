@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan anggaran & aktivitas pengajuan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Greeting --}}
<div class="mb-6 animate-page">
    <h2 class="text-xl font-display text-slate-900">
        Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 17 ? 'siang' : 'malam') }},
        <span class="text-indigo-600">{{ Str::words($user->name, 1, '') }}</span> 👋
    </h2>
    <p class="text-sm text-slate-500 mt-0.5">
        {{ $user->department?->dept_name ?? 'Tanpa Departemen' }}
        @if(isset($userCostCenter) && $userCostCenter)
            · <span class="text-indigo-500 font-medium">{{ $userCostCenter->cost_center_name }}</span>
            @if($userCostCenter->cost_center_code)
                <span class="text-slate-400">({{ $userCostCenter->cost_center_code }})</span>
            @endif
        @endif
        · Tahun Fiskal {{ now()->year }}
    </p>
</div>

{{-- Pending Reviews Notification --}}
@if(isset($pendingReviews) && $pendingReviews > 0)
<div class="mb-6 animate-page">
    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-start gap-4 shadow-sm">
        <div class="flex-shrink-0 bg-rose-100 rounded-full p-2 mt-0.5">
            <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-rose-800">Ada dokumen yang perlu direview!</h3>
            <p class="text-sm text-rose-700 mt-1">
                Terdapat <strong class="font-bold">{{ $pendingReviews }}</strong> dokumen pengajuan yang sedang menunggu persetujuan/penolakan dari Anda. Mohon segera diproses.
            </p>
            <div class="mt-3">
                <a href="{{ route('tracking.index') }}" class="inline-flex items-center text-xs font-semibold text-rose-800 hover:text-rose-900 bg-rose-200/50 hover:bg-rose-200 px-3 py-1.5 rounded-lg transition-colors">
                    Lihat Daftar Pengajuan &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Summary Cards --}}
{{-- Contoh penggunaan <x-stat-card> di dashboard.blade.php --}}

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <x-stat-card
        title="Sisa Pagu"
        value="Rp {{ number_format($sisaPagu, 0, ',', '.') }}"
        subtitle="dari Rp {{ number_format($totalPlan, 0, ',', '.') }}"
        color="emerald"
        delay="animate-page">
        {{-- Icon slot --}}
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
        </svg>
    </x-stat-card>

    <x-stat-card
        title="Pengajuan Berjalan"
        value="Rp {{ number_format($totalPengajuanBerjalan, 0, ',', '.') }}"
        subtitle="Sedang dalam proses review"
        color="amber"
        delay="animate-page-delay-1">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </x-stat-card>

    <x-stat-card
        title="Realisasi Selesai"
        value="Rp {{ number_format($totalPengajuanSelesai, 0, ',', '.') }}"
        subtitle="IA sudah Approved & Closed"
        color="indigo"
        delay="animate-page-delay-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </x-stat-card>

</div>

{{-- Charts Section --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Main Chart: Rencana vs Realisasi --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 p-5 shadow-sm animate-page-delay-3">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Tren Rencana vs Realisasi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbandingan per bulan — Tahun {{ now()->year }}</p>
            </div>
            <span class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-0 border-t-2 border-dashed border-indigo-500 inline-block"></span> Pagu
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-indigo-600 inline-block"></span> Realisasi
                </span>
            </span>
        </div>
        <div class="relative h-56 sm:h-72">
            <canvas id="budgetChart"></canvas>
        </div>
    </div>

    {{-- Donut Chart: Komposisi Anggaran --}}
    <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm animate-page-delay-3">
        <div class="mb-5">
            <h3 class="text-sm font-semibold text-slate-800">Komposisi Pagu Departemen</h3>
            <p class="text-xs text-slate-400 mt-0.5">Status penyerapan anggaran saat ini</p>
        </div>
        <div class="relative h-48 flex items-center justify-center">
            <canvas id="compositionChart"></canvas>
            {{-- Text in middle of donut --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-xs text-slate-400 font-medium">Sisa Pagu</span>
                <span class="text-sm font-bold text-emerald-600">
                    {{ $sisaPagu >= 1000000 ? round($sisaPagu / 1000000) . ' Jt' : number_format($sisaPagu, 0, ',', '.') }}
                </span>
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Sisa Pagu</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($sisaPagu, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Di-hold (Review)</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($totalReserved, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Terealisasi</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($totalUsed, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Transaksi Anggaran Terbaru --}}
<div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm animate-page-delay-4 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Riwayat Pemakaian Anggaran Terbaru</h3>
            <p class="text-xs text-slate-400 mt-0.5">Mendetailkan dokumen yang sedang hold dan merealisasikan anggaran</p>
        </div>
        <a href="{{ route('budget-logs.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
    </div>

    @if(isset($recentBudgetLogs) && $recentBudgetLogs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="p-3 font-semibold rounded-tl-lg">Tanggal</th>
                        <th class="p-3 font-semibold">Tipe</th>
                        <th class="p-3 font-semibold">No. Referensi</th>
                        <th class="p-3 font-semibold">Keterangan</th>
                        <th class="p-3 font-semibold text-right rounded-tr-lg">Nominal</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @foreach($recentBudgetLogs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 text-slate-600 whitespace-nowrap">
                                {{ $log->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-3">
                                @if($log->log_type === 'reserve')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Hold / Proses</span>
                                @elseif($log->log_type === 'actual_deduction')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Terealisasi</span>
                                @elseif($log->log_type === 'increase')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Penambahan</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">{{ $log->log_type }}</span>
                                @endif
                            </td>
                            <td class="p-3 font-medium text-slate-700 whitespace-nowrap">
                                {{ $log->reference_no }}
                            </td>
                            <td class="p-3 text-slate-500 min-w-[200px]">
                                {{ $log->description }}
                            </td>
                            <td class="p-3 text-right font-semibold whitespace-nowrap 
                                {{ $log->log_type === 'increase' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $log->log_type === 'increase' ? '+' : '-' }} Rp {{ number_format($log->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-slate-500 text-sm">Belum ada riwayat pemakaian anggaran.</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const labels      = @json($labels);
const dataRencana  = @json($dataRencana);
const dataRealisasi = @json($dataRealisasi);

const ctx = document.getElementById('budgetChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                type: 'line',
                label: 'Pagu Tahunan (Rencana)',
                data: dataRencana,
                backgroundColor: 'transparent',
                borderColor: 'rgba(99, 102, 241, 0.5)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
                order: 1
            },
            {
                type: 'bar',
                label: 'Realisasi (Kumulatif)',
                data: dataRealisasi,
                backgroundColor: 'rgba(99, 102, 241, 0.85)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
                order: 2
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#94a3b8',
                bodyColor: '#f1f5f9',
                padding: 10,
                cornerRadius: 10,
                callbacks: {
                    label: ctx => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { color: '#94a3b8', font: { size: 11 } },
            },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                border: { display: false, dash: [4,4] },
                ticks: {
                    color: '#94a3b8',
                    font: { size: 11 },
                    callback: v => 'Rp ' + (v / 1000000).toLocaleString('id-ID') + ' Jt',
                },
            },
        },
    },
});

// Komposisi Anggaran (Donut Chart)
const sisaPagu      = {{ (float) $sisaPagu }};
const totalReserved = {{ (float) $totalReserved }};
const totalUsed     = {{ (float) $totalUsed }};

if (document.getElementById('compositionChart')) {
    const ctxDonut = document.getElementById('compositionChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Sisa Pagu', 'Di-hold (Review)', 'Terealisasi'],
            datasets: [{
                data: [sisaPagu, totalReserved, totalUsed],
                backgroundColor: [
                    '#10b981', // emerald-500
                    '#fbbf24', // amber-400
                    '#4f46e5', // indigo-600
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                            return label;
                        }
                    }
                }
            }
        }
    });
}
</script>
@endpush