@extends('layouts.superadmin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem e-budgeting secara keseluruhan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Greeting --}}
<div class="mb-6 animate-page">
    <h2 class="text-xl font-display text-slate-900">
        Selamat {{ now()->hour < 12 ? 'pagi' : (now()->hour < 17 ? 'siang' : 'malam') }},
        <span class="text-violet-600">{{ Str::words(Auth::user()->name, 1, '') }}</span> ⚙️
    </h2>
    <p class="text-sm text-slate-500 mt-0.5">Panel Superadmin · Tahun Fiskal {{ $year }}</p>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Card: Total Users --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-shadow animate-page">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total User</span>
            <span class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </span>
        </div>
        <p class="text-2xl font-bold text-slate-900 animate-count">{{ $totalUsers }}</p>
        <p class="text-xs text-slate-400 mt-1">Pengguna aktif terdaftar</p>
    </div>

    {{-- Card: Total Departments --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-shadow animate-page-delay-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Departemen</span>
            <span class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                </svg>
            </span>
        </div>
        <p class="text-2xl font-bold text-slate-900 animate-count">{{ $totalDepartments }}</p>
        <p class="text-xs text-slate-400 mt-1">Departemen terdaftar</p>
    </div>

    {{-- Card: Total Budget --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-shadow animate-page-delay-2">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Anggaran</span>
            <span class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
                </svg>
            </span>
        </div>
        <p class="text-2xl font-bold text-slate-900 animate-count">@format_rupiah($totalBudgetPlan)</p>
        <p class="text-xs text-slate-400 mt-1">Terpakai: @format_rupiah($totalBudgetUsed)</p>
    </div>

    {{-- Card: Pengajuan Aktif --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-shadow animate-page-delay-3">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengajuan Aktif</span>
            <span class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <p class="text-2xl font-bold text-slate-900 animate-count">{{ $totalPengajuanAktif }}</p>
        <p class="text-xs text-slate-400 mt-1">Draft & In Review</p>
    </div>

</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

    {{-- Budget per Departemen --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 gap-2">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Anggaran per Departemen</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbandingan Pagu vs Realisasi — {{ $year }}</p>
            </div>
            <span class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-violet-200 inline-block"></span> Pagu
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-violet-600 inline-block"></span> Terpakai
                </span>
            </span>
        </div>
        <div class="relative h-48 sm:h-64">
            <canvas id="deptBudgetChart"></canvas>
        </div>
    </div>

    {{-- Tren Realisasi Bulanan --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Tren Realisasi Bulanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Total realisasi semua departemen — {{ $year }}</p>
            </div>
        </div>
        <div class="relative h-48 sm:h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

</div>

{{-- Bottom Row: Alerts + Recent Activities --}}
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
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500">Semua departemen dalam batas aman</p>
                <p class="text-xs text-slate-400 mt-0.5">Utilisasi di bawah 80%</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($highUtilDepts as $dept)
                    @php $pct = round(($dept['used'] / $dept['plan']) * 100, 1); @endphp
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm font-medium text-amber-800">{{ $dept['name'] }}</span>
                            <span class="text-xs font-bold {{ $pct >= 90 ? 'text-rose-600' : 'text-amber-600' }}">{{ $pct }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-amber-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-rose-500' : 'bg-amber-500' }} transition-all duration-500"
                                 style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <p class="text-[11px] text-amber-600 mt-1">Rp {{ number_format($dept['used'], 0, ',', '.') }} / Rp {{ number_format($dept['plan'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Activities --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
            <span class="text-xs text-slate-400">10 pengajuan terakhir</span>
        </div>

        <div class="overflow-x-auto -mx-5 px-5">
            {{-- Desktop Table --}}
            <table class="w-full text-sm hidden sm:table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">No. PH</th>
                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Subject</th>
                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaju</th>
                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Dept</th>
                        <th class="text-right py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nominal</th>
                        <th class="text-center py-2.5 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentActivities as $ph)
                    <tr class="hover:bg-slate-25 transition-colors">
                        <td class="py-2.5 px-3 font-mono text-xs text-slate-600">{{ $ph->ph_number }}</td>
                        <td class="py-2.5 px-3 text-slate-700 max-w-[200px] truncate">{{ $ph->subject }}</td>
                        <td class="py-2.5 px-3 text-slate-600">{{ $ph->ppbj?->user?->name ?? '-' }}</td>
                        <td class="py-2.5 px-3 text-slate-500 text-xs">{{ $ph->ppbj?->user?->department?->dept_name ?? '-' }}</td>
                        <td class="py-2.5 px-3 text-right font-medium text-slate-700">Rp {{ number_format($ph->nominal_request, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                {{ match($ph->status) {
                                    'Approved'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'In_Review' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'Rejected'  => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    default     => 'bg-slate-50 text-slate-600 border border-slate-200',
                                } }}">
                                {{ str_replace('_', ' ', $ph->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada pengajuan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile Card View --}}
            <div class="sm:hidden space-y-3">
                @forelse($recentActivities as $ph)
                <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-100">
                    <div class="flex items-start justify-between mb-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $ph->subject }}</p>
                            <p class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $ph->ph_number }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0 ml-2
                            {{ match($ph->status) {
                                'Approved'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                'In_Review' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'Rejected'  => 'bg-rose-50 text-rose-700 border border-rose-200',
                                default     => 'bg-slate-50 text-slate-600 border border-slate-200',
                            } }}">
                            {{ str_replace('_', ' ', $ph->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">{{ $ph->ppbj?->user?->name ?? '-' }} · {{ $ph->ppbj?->user?->department?->dept_name ?? '-' }}</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($ph->nominal_request, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <p class="py-8 text-center text-slate-400 text-sm">Belum ada pengajuan</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Budget per Departemen Chart ──────────────────────────
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
                backgroundColor: 'rgba(139, 92, 246, 0.15)',
                borderColor: 'rgba(139, 92, 246, 0.3)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Terpakai',
                data: deptUsed,
                backgroundColor: 'rgba(139, 92, 246, 0.85)',
                borderColor: 'rgba(139, 92, 246, 1)',
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

// ── Tren Realisasi Bulanan Chart ─────────────────────────
const monthLabels    = @json($labels);
const monthRealisasi = @json($dataRealisasi);

new Chart(document.getElementById('monthlyChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Realisasi',
            data: monthRealisasi,
            borderColor: 'rgba(139, 92, 246, 1)',
            backgroundColor: 'rgba(139, 92, 246, 0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: 'rgba(139, 92, 246, 1)',
            pointBorderWidth: 2,
            pointHoverRadius: 6,
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
