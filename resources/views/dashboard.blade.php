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
    <p class="text-sm text-slate-500 mt-0.5">{{ $user->department?->dept_name ?? 'Tanpa Departemen' }} · Tahun Fiskal {{ now()->year }}</p>
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
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
        </svg>
    </x-stat-card>

    <x-stat-card
        title="Pengajuan Berjalan"
        value="Rp {{ number_format($totalPengajuanBerjalan, 0, ',', '.') }}"
        subtitle="Sedang dalam proses review"
        color="amber"
        delay="animate-page-delay-1">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </x-stat-card>

    <x-stat-card
        title="Realisasi Selesai"
        value="Rp {{ number_format($totalPengajuanSelesai, 0, ',', '.') }}"
        subtitle="IA sudah Approved & Closed"
        color="indigo"
        delay="animate-page-delay-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </x-stat-card>

</div>

{{-- Tombol Input Budget (Khusus Ka.Dept) --}}
@if(Auth::user()->isKaDept())
<div class="mb-5 animate-page">
    <a href="{{ route('budget.create') }}"
       class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700
              text-white text-sm font-semibold transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Input Budget Anggaran
    </a>
</div>
@endif

{{-- Chart --}}
<div class="bg-white rounded-1xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Rencana vs Realisasi Anggaran</h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbandingan per bulan — Tahun {{ now()->year }}</p>
        </div>
        <span class="flex items-center gap-3 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-indigo-200 inline-block"></span> Rencana
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span> Realisasi
            </span>
        </span>
    </div>
    <div class="relative h-56 sm:h-72">
        <canvas id="budgetChart"></canvas>
    </div>
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
                label: 'Rencana',
                data: dataRencana,
                backgroundColor: 'rgba(99, 102, 241, 0.12)',
                borderColor: 'rgba(99, 102, 241, 0.3)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Realisasi',
                data: dataRealisasi,
                backgroundColor: 'rgba(99, 102, 241, 0.85)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
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
</script>
@endpush