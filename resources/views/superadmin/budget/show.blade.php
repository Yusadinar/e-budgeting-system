@extends('layouts.superadmin')
@section('title', 'Budget — ' . $department->dept_name)
@section('page-title', 'Detail Budget')
@section('page-subtitle', $department->dept_name . ' — Tahun ' . $year)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Back --}}
<a href="{{ route('superadmin.budget.index') }}"
   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-violet-600 transition-colors mb-5 animate-page">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali ke Budget Overview
</a>

{{-- Department Header --}}
<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card mb-5 animate-page">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-50 to-indigo-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800">{{ $department->dept_name }}</h2>
            <p class="text-xs text-slate-400 font-mono">{{ $department->budget_code }} · FY {{ $year }}</p>
        </div>
    </div>
</div>

{{-- Budget Cards --}}
@if($budget)
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-1">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pagu</span>
        <p class="text-xl font-bold text-slate-900 mt-1.5">Rp {{ number_format($budget->total_plan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-1">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Terpakai</span>
        <p class="text-xl font-bold text-violet-600 mt-1.5">Rp {{ number_format($budget->total_used, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-2">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Reserved</span>
        <p class="text-xl font-bold text-amber-600 mt-1.5">Rp {{ number_format($budget->total_reserved, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card animate-page-delay-2">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sisa</span>
        <p class="text-xl font-bold {{ $budget->remaining < 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1.5">
            Rp {{ number_format($budget->remaining, 0, ',', '.') }}
        </p>
    </div>
</div>
@else
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-700 mb-6 animate-page-delay-1">
    Belum ada data budget untuk departemen ini di tahun {{ $year }}.
</div>
@endif

{{-- Chart --}}
<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card mb-6 animate-page-delay-3">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Realisasi Bulanan</h3>
            <p class="text-xs text-slate-400 mt-0.5">IA Approved per bulan — Tahun {{ $year }}</p>
        </div>
    </div>
    <div class="relative h-64">
        <canvas id="realisasiChart"></canvas>
    </div>
</div>

{{-- Audit Logs --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-4">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Riwayat Perubahan Anggaran</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar log audit</p>
        </div>
        
        <form method="GET" action="{{ route('superadmin.budget.show', $department) }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
            
            <select name="log_type" class="text-sm rounded-xl border-slate-200 focus:ring-violet-500 focus:border-violet-500 w-full sm:w-auto px-4 py-2">
                <option value="">Semua Tipe</option>
                <option value="reserve" {{ request('log_type') == 'reserve' ? 'selected' : '' }}>Reserve (Hold)</option>
                <option value="actual_deduction" {{ request('log_type') == 'actual_deduction' ? 'selected' : '' }}>Realisasi</option>
                <option value="increase" {{ request('log_type') == 'increase' ? 'selected' : '' }}>Penambahan Pagu</option>
                <option value="decrease" {{ request('log_type') == 'decrease' ? 'selected' : '' }}>Pengurangan Pagu</option>
                <option value="reclass" {{ request('log_type') == 'reclass' ? 'selected' : '' }}>Reklasifikasi</option>
            </select>
            
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi / referensi..." class="text-sm rounded-xl border-slate-200 focus:ring-violet-500 focus:border-violet-500 px-4 py-2 w-full sm:w-64">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-slate-700 transition-colors">
                    Filter
                </button>
                @if(request('search') || request('log_type'))
                    <a href="{{ route('superadmin.budget.show', ['department' => $department, 'year' => request('year', now()->year)]) }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Tipe</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Referensi</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Nominal</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-full">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                <tr class="hover:bg-violet-50/30 transition-colors">
                    <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3 px-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ match($log->log_type) {
                                'reserve'          => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'actual_deduction'  => 'bg-violet-50 text-violet-700 border border-violet-200',
                                'increase'          => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                default             => 'bg-slate-50 text-slate-600 border border-slate-200',
                            } }}">
                            {{ $log->log_type_label }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $log->reference_no }}</td>
                    <td class="py-3 px-4 text-right font-medium text-slate-700 whitespace-nowrap">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-xs text-slate-500 max-w-sm truncate" title="{{ $log->description }}">{{ $log->description ?: '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Tidak ada log yang sesuai dengan filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const labels = @json($labels);
const dataRealisasi = @json($dataRealisasi);

new Chart(document.getElementById('realisasiChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Realisasi',
            data: dataRealisasi,
            backgroundColor: 'rgba(139, 92, 246, 0.75)',
            borderColor: 'rgba(139, 92, 246, 1)',
            borderWidth: 0,
            borderRadius: 6,
            borderSkipped: false,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e1b4b',
                titleColor: '#a5b4fc',
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
                border: { display: false },
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
