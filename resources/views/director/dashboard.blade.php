@extends('layouts.app')
@section('title', 'Dashboard Direktur')
@section('page-title', 'Dashboard Direktur')
@section('page-subtitle')
    Monitoring anggaran & kinerja perusahaan — Tahun Fiskal {{ now()->year }}
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Greeting --}}
<div class="mb-6 animate-page">
    <h2 class="text-xl font-display text-slate-900">
        Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 17 ? 'siang' : 'malam') }},
        <span class="text-amber-600">{{ Str::words($user->name, 1, '') }}</span> 🏢
    </h2>
    <p class="text-sm text-slate-500 mt-0.5">
        {{ match($user->role) { 'man_dir' => 'Manufacturing Director', 'fin_dir' => 'Finance & Human Capital Director', 'prod_dir' => 'Production Director', 'pres_dir' => 'President Director', default => 'Director Dashboard' } }}
        · Monitoring Perusahaan · FY{{ $year }}
    </p>
</div>

{{-- Pending IA Approval Alert --}}
@if($pendingApprovalDir > 0)
<div class="mb-6 animate-page">
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-4 shadow-sm">
        <div class="flex-shrink-0 bg-amber-100 rounded-full p-2 mt-0.5">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-800">Persetujuan Anda Dibutuhkan</h3>
            <p class="text-sm text-amber-700 mt-1">
                Terdapat <strong>{{ $pendingApprovalDir }}</strong> dokumen pengajuan yang menunggu persetujuan Anda.
            </p>
            <a href="{{ route('tracking.index') }}" class="inline-flex items-center mt-2 text-xs font-semibold text-amber-800 hover:text-amber-900 bg-amber-200/50 hover:bg-amber-200 px-3 py-1.5 rounded-lg transition-colors">
                Buka Daftar Pengajuan &rarr;
            </a>
        </div>
    </div>
</div>
@endif

{{-- Row 1: 4 KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    {{-- Total Budget Plan --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Pagu</span>
            <span class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/></svg>
            </span>
        </div>
        <p class="text-lg font-bold text-slate-900 leading-tight">@format_rupiah($totalBudgetPlan)</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalDepartments }} departemen aktif</p>
    </div>

    {{-- Terpakai --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-1">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Terpakai</span>
            <span class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.511-5.511-3.182"/></svg>
            </span>
        </div>
        <p class="text-lg font-bold text-slate-900 leading-tight">@format_rupiah($totalBudgetUsed)</p>
        <div class="flex items-center gap-1.5 mt-1">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $pctUsed >= 85 ? 'bg-rose-500' : ($pctUsed >= 65 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min($pctUsed, 100) }}%"></div>
            </div>
            <span class="text-[11px] font-semibold {{ $pctUsed >= 85 ? 'text-rose-600' : 'text-slate-500' }}">{{ $pctUsed }}%</span>
        </div>
    </div>

    {{-- Sisa Pagu --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-2">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sisa Pagu</span>
            <span class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <p class="text-lg font-bold text-slate-900 leading-tight">@format_rupiah($totalBudgetSisa)</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Reserve: @format_rupiah($totalBudgetReserved)</p>
    </div>

    {{-- Pengajuan Aktif --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-3">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pengajuan</span>
            <span class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <p class="text-lg font-bold text-slate-900 leading-tight">{{ $totalPengajuanAktif }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">
            <span class="text-emerald-600 font-medium">{{ $totalPengajuanApproved }}</span> approved ·
            <span class="text-rose-500 font-medium">{{ $totalPengajuanRejected }}</span> rejected
        </p>
    </div>

</div>

{{-- Row 2: Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-5">

    {{-- Bar Chart: Anggaran per Departemen (3/5 width) --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Anggaran per Departemen</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pagu vs Terpakai — FY{{ $year }}</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-200 inline-block"></span>Pagu</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>Terpakai</span>
            </div>
        </div>
        <div class="relative h-52 sm:h-64">
            <canvas id="deptBudgetChart"></canvas>
        </div>
    </div>

    {{-- Line Chart: Tren Realisasi Bulanan (2/5 width) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Tren Realisasi Bulanan</h3>
            <p class="text-xs text-slate-400 mt-0.5">IA Approved per bulan — FY{{ $year }}</p>
        </div>
        <div class="relative h-52 sm:h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

</div>

{{-- Row 3: Dept Alerts + Pending IA --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Budget Alerts --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-4">
        <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
            </svg>
            Budget Alert
        </h3>
        @if($highUtilDepts->isEmpty())
        <div class="text-center py-8">
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-slate-500">Semua departemen dalam batas aman</p>
            <p class="text-xs text-slate-400 mt-0.5">Utilisasi di bawah 75%</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($highUtilDepts as $dept)
            @php $pct = round(($dept['used'] / $dept['plan']) * 100, 1); @endphp
            <div class="p-3 rounded-xl {{ $pct >= 90 ? 'bg-rose-50 border border-rose-100' : 'bg-amber-50 border border-amber-100' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold {{ $pct >= 90 ? 'text-rose-800' : 'text-amber-800' }} truncate max-w-[140px]">{{ $dept['name'] }}</span>
                    <span class="text-xs font-bold {{ $pct >= 90 ? 'text-rose-600' : 'text-amber-600' }}">{{ $pct }}%</span>
                </div>
                <div class="w-full h-1.5 bg-white/60 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-rose-500' : 'bg-amber-500' }}" style="width: {{ min($pct, 100) }}%"></div>
                </div>
                <p class="text-[10px] {{ $pct >= 90 ? 'text-rose-600' : 'text-amber-600' }} mt-1">Rp {{ number_format($dept['used']/1_000_000, 0, ',', '.') }} Jt / Rp {{ number_format($dept['plan']/1_000_000, 0, ',', '.') }} Jt</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- IA Pending Approval Direktur --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Menunggu Persetujuan Anda</h3>
            @if($pendingApprovalDir > 0)
            <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full">{{ $pendingApprovalDir }} Pending</span>
            @endif
        </div>

        @if($pendingList->isEmpty())
        <div class="text-center py-8">
            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm text-slate-500">Tidak ada dokumen yang menunggu</p>
        </div>
        @else
        <div class="space-y-2.5">
            @foreach($pendingList as $item)
            <a href="{{ route('tracking.show', $item->id) }}" class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors group">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate group-hover:text-indigo-600 transition-colors">{{ $item->latestProposalHarga->subject ?? $item->ppbj_number }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[11px] text-slate-400">{{ $item->user->name ?? '-' }}</span>
                        <span class="text-slate-300">·</span>
                        <span class="text-[11px] text-slate-400">{{ $item->user->department->dept_name ?? '-' }}</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $item->updated_at->diffForHumans() }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        @if($pendingApprovalDir > 0)
        <div class="mt-4 pt-3 border-t border-slate-100">
            <a href="{{ route('tracking.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                Lihat semua pengajuan &rarr;
            </a>
        </div>
        @endif
    </div>

</div>

{{-- Row 4: Detail Budget Cost Center --}}
<div class="mt-5 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-5">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Detail Anggaran per Cost Center</h3>
            <p class="text-xs text-slate-400 mt-0.5">Monitoring serapan budget hingga level operasional terkecil (FY{{ $year }})</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cost Center</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Departemen</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pagu</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Terpakai</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider" style="min-width: 120px">Utilisasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($costCenterBudgets as $cc)
                <tr class="hover:bg-violet-50/30 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-800">{{ $cc['name'] }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $cc['code'] ?: 'N/A' }}</div>
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-500">{{ $cc['dept_name'] }}</td>
                    <td class="py-3 px-4 text-right text-slate-700">Rp {{ number_format($cc['plan'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4 text-right text-violet-600 font-medium">Rp {{ number_format($cc['used'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4 text-right {{ $cc['sisa'] < 0 ? 'text-rose-600' : 'text-emerald-600' }} font-medium">Rp {{ number_format($cc['sisa'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700
                                     {{ $cc['utilization'] >= 90 ? 'bg-rose-500' : ($cc['utilization'] >= 75 ? 'bg-amber-500' : 'bg-violet-500') }}"
                                     style="width: {{ min($cc['utilization'], 100) }}%"></div>
                            </div>
                            <span class="text-[10px] font-semibold w-8 text-right
                                {{ $cc['utilization'] >= 90 ? 'text-rose-600' : ($cc['utilization'] >= 75 ? 'text-amber-600' : 'text-violet-600') }}">
                                {{ $cc['utilization'] }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Data detail cost center belum tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Budget per Departemen ─────────────────────────────
const deptLabels = @json($chartDeptLabels);
const deptPlan   = @json($chartDeptPlan);
const deptUsed   = @json($chartDeptUsed);

new Chart(document.getElementById('deptBudgetChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: deptLabels,
        datasets: [
            {
                label: 'Pagu',
                data: deptPlan,
                backgroundColor: 'rgba(99,102,241,0.12)',
                borderColor: 'rgba(99,102,241,0.3)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Terpakai',
                data: deptUsed,
                backgroundColor: 'rgba(99,102,241,0.85)',
                borderColor: 'rgba(99,102,241,1)',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
            },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e1b4b', titleColor: '#a5b4fc', bodyColor: '#f1f5f9',
                padding: 10, cornerRadius: 10,
                callbacks: { label: ctx => ' Rp ' + (ctx.parsed.y / 1_000_000).toLocaleString('id-ID') + ' Jt' },
            },
        },
        scales: {
            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 35 } },
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => 'Rp ' + (v/1_000_000_000).toFixed(1) + ' M' } },
        },
    },
});

// ── Tren Realisasi Bulanan ────────────────────────────
const monthLabels    = @json($labels);
const monthRealisasi = @json($dataRealisasi);

new Chart(document.getElementById('monthlyChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Realisasi',
            data: monthRealisasi,
            borderColor: 'rgba(245,158,11,1)',
            backgroundColor: 'rgba(245,158,11,0.08)',
            borderWidth: 2.5,
            fill: true, tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: 'rgba(245,158,11,1)',
            pointBorderWidth: 2,
            pointHoverRadius: 6,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e1b4b', titleColor: '#a5b4fc', bodyColor: '#f1f5f9',
                padding: 10, cornerRadius: 10,
                callbacks: { label: ctx => ' Rp ' + (ctx.parsed.y / 1_000_000).toLocaleString('id-ID') + ' Jt' },
            },
        },
        scales: {
            x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => 'Rp ' + (v/1_000_000).toLocaleString('id-ID') + ' Jt' } },
        },
    },
});
</script>
@endpush
