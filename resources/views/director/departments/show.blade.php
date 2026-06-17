@extends('layouts.app')
@section('title', 'Detail Departemen — ' . $department->dept_name)
@section('page-title', $department->dept_name)
@section('page-subtitle')
    Detail monitoring departemen · FY{{ $year }}
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Back Button --}}
<a href="{{ route('director.departments.index') }}?year={{ $year }}"
   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-5 animate-page">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Kembali ke Daftar Departemen
</a>

{{-- Department Header --}}
<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card mb-5 animate-page">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-indigo-500/20">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $department->dept_name }}</h2>
                <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $department->budget_code }} · {{ $members->count() }} anggota · FY {{ $year }}</p>
            </div>
        </div>
        {{-- Year Switcher --}}
        <form method="GET" action="{{ route('director.departments.show', $department) }}" class="flex items-center gap-2">
            <label class="text-xs text-slate-500">Tahun:</label>
            <select name="year" onchange="this.form.submit()"
                    class="text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
</div>

{{-- Budget KPI Cards --}}
@if($budget)
@php
    $pct = $budget->total_plan > 0 ? round(($budget->total_used / $budget->total_plan) * 100, 1) : 0;
    $barColor = $pct >= 90 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : ($pct >= 50 ? 'bg-indigo-500' : 'bg-emerald-500'));
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5 animate-page-delay-1">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pagu Anggaran</span>
        <p class="text-xl font-bold text-slate-900 mt-1.5 leading-tight">Rp {{ number_format($budget->total_plan / 1_000_000, 0, ',', '.') }} Jt</p>
        <p class="text-[11px] text-slate-400 mt-0.5">FY{{ $year }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Terpakai</span>
        <p class="text-xl font-bold text-indigo-600 mt-1.5 leading-tight">Rp {{ number_format($budget->total_used / 1_000_000, 0, ',', '.') }} Jt</p>
        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ min($pct, 100) }}%"></div>
        </div>
        <p class="text-[11px] {{ $pct >= 85 ? 'text-rose-500 font-semibold' : 'text-slate-400' }} mt-0.5">{{ $pct }}%</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Reserved</span>
        <p class="text-xl font-bold text-amber-600 mt-1.5 leading-tight">Rp {{ number_format($budget->total_reserved / 1_000_000, 0, ',', '.') }} Jt</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Pengajuan in-review</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sisa Pagu</span>
        <p class="text-xl font-bold {{ $budget->remaining < 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1.5 leading-tight">
            Rp {{ number_format($budget->remaining / 1_000_000, 0, ',', '.') }} Jt
        </p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $budget->remaining < 0 ? '⚠ Melebihi Pagu' : 'Tersisa' }}</p>
    </div>
</div>
@else
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-700 mb-5 animate-page-delay-1 flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
    </svg>
    Belum ada data anggaran untuk departemen ini di tahun <strong>{{ $year }}</strong>.
</div>
@endif

{{-- Pengajuan Stats + Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-5">

    {{-- Chart Realisasi Bulanan (3/5) --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Realisasi Bulanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">IA Approved per bulan — FY{{ $year }}</p>
            </div>
        </div>
        <div class="relative h-52">
            <canvas id="realisasiChart"></canvas>
        </div>
    </div>

    {{-- Pengajuan Stats (2/5) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-2 flex flex-col">
        <h3 class="text-sm font-semibold text-slate-800 mb-4">Statistik Pengajuan</h3>
        <div class="space-y-3 flex-1">
            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl border border-amber-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                    <span class="text-sm text-slate-700">Aktif / Proses</span>
                </div>
                <span class="text-lg font-bold text-amber-700">{{ $statAktif }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                    <span class="text-sm text-slate-700">Disetujui</span>
                </div>
                <span class="text-lg font-bold text-emerald-700">{{ $statApproved }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-rose-50 rounded-xl border border-rose-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                    <span class="text-sm text-slate-700">Ditolak</span>
                </div>
                <span class="text-lg font-bold text-rose-700">{{ $statRejected }}</span>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-400 text-center">Total: {{ $statAktif + $statApproved + $statRejected }} pengajuan FY{{ $year }}</p>
        </div>
    </div>

</div>

{{-- Anggota Departemen + Daftar Pengajuan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

    {{-- Daftar Anggota (1/3) --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-3">
        <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            Anggota Departemen
            <span class="ml-auto text-xs bg-indigo-100 text-indigo-600 font-semibold px-2 py-0.5 rounded-full">{{ $members->count() }}</span>
        </h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            @forelse($members as $member)
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                    <span class="text-indigo-600 text-xs font-semibold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-slate-700 truncate">{{ $member->name }}</p>
                    <p class="text-[11px] text-slate-400">
                        {{ match($member->role) {
                            'staff'       => 'Staff',
                            'ka_sie'      => 'Ka. Seksi',
                            'ka_dept'     => 'Ka. Departemen',
                            'ka_div'      => 'Ka. Divisi',
                            'accounting'  => 'Accounting',
                            'ka_dept_acc' => 'Ka. Dept Acc',
                            'ka_div_acc'  => 'Ka. Div Acc',
                            default       => ucfirst($member->role),
                        } }}
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-4">Belum ada anggota</p>
            @endforelse
        </div>
    </div>

    {{-- Daftar Pengajuan (2/3) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-3">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800">Riwayat Pengajuan</h3>
            @if($statAktif > 0)
            <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full">{{ $statAktif }} aktif</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100">
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Subject</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Pengaju</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Nominal</th>
                        <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pengajuanList as $ph)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-700 truncate max-w-[160px]">{{ $ph->subject }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $ph->updated_at->format('d M Y') }}</p>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-slate-600 text-xs">{{ $ph->ppbj?->user?->name ?? '-' }}</p>
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-slate-800 whitespace-nowrap">
                            Rp {{ number_format($ph->internalAgreement?->final_nominal ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $finalStatus = 'Proses';
                                $colorClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                
                                if ($ph->status === 'Rejected' || ($ph->internalAgreement && $ph->internalAgreement->status_ia === 'Rejected')) {
                                    $finalStatus = 'Ditolak';
                                    $colorClass = 'bg-rose-50 text-rose-700 border border-rose-200';
                                } elseif ($ph->status === 'Approved' && $ph->internalAgreement && $ph->internalAgreement->status_ia === 'Approved') {
                                    $finalStatus = 'Disetujui';
                                    $colorClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                } elseif ($ph->status === 'Approved' && !$ph->internalAgreement) {
                                    $finalStatus = 'Proses (Menunggu IA)';
                                    $colorClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                } else {
                                    $finalStatus = 'Proses';
                                    $colorClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $colorClass }}">
                                {{ $finalStatus }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('tracking.show', $ph->ppbj_id) }}"
                               class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors whitespace-nowrap">
                                Detail →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-sm">
                            Belum ada pengajuan dari departemen ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuanList->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $pengajuanList->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Detail Budget Cost Center --}}
<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card mb-5 animate-page-delay-3">
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-slate-800">Detail Anggaran per Cost Center</h3>
        <p class="text-xs text-slate-400 mt-0.5">Monitoring serapan budget hingga level operasional terkecil (FY{{ $year }})</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cost Center</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pagu</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Terpakai</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider" style="min-width: 120px">Utilisasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($costCenterBudgets as $cc)
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-slate-800">{{ $cc['name'] }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $cc['code'] ?: 'N/A' }}</div>
                    </td>
                    <td class="py-3 px-4 text-right text-slate-700">Rp {{ number_format($cc['plan'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4 text-right text-indigo-600 font-medium">Rp {{ number_format($cc['used'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4 text-right {{ $cc['sisa'] < 0 ? 'text-rose-600' : 'text-emerald-600' }} font-medium">Rp {{ number_format($cc['sisa'] / 1000000, 0, ',', '.') }} Jt</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700
                                     {{ $cc['utilization'] >= 90 ? 'bg-rose-500' : ($cc['utilization'] >= 75 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                                     style="width: {{ min($cc['utilization'], 100) }}%"></div>
                            </div>
                            <span class="text-[10px] font-semibold w-8 text-right
                                {{ $cc['utilization'] >= 90 ? 'text-rose-600' : ($cc['utilization'] >= 75 ? 'text-amber-600' : 'text-indigo-600') }}">
                                {{ $cc['utilization'] }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Data detail cost center belum tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Budget Logs / Audit Trail --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-4">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Riwayat Perubahan Anggaran</h3>
            <p class="text-xs text-slate-400 mt-0.5">Audit log mutasi anggaran departemen</p>
        </div>
        <form method="GET" action="{{ route('director.departments.show', $department) }}" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="year" value="{{ $year }}">
            <select name="log_type" class="text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5">
                <option value="">Semua Tipe</option>
                <option value="reserve"          {{ request('log_type') == 'reserve'          ? 'selected' : '' }}>Reserve</option>
                <option value="actual_deduction"  {{ request('log_type') == 'actual_deduction' ? 'selected' : '' }}>Realisasi</option>
                <option value="increase"          {{ request('log_type') == 'increase'         ? 'selected' : '' }}>Penambahan</option>
                <option value="decrease"          {{ request('log_type') == 'decrease'         ? 'selected' : '' }}>Pengurangan</option>
            </select>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari referensi / keterangan..."
                       class="text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5 w-full sm:w-56">
                <button type="submit" class="bg-slate-800 text-white px-4 py-1.5 rounded-xl text-sm font-medium hover:bg-slate-700 transition-colors whitespace-nowrap">Filter</button>
                @if(request('search') || request('log_type'))
                <a href="{{ route('director.departments.show', ['department' => $department, 'year' => $year]) }}"
                   class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors whitespace-nowrap">Reset</a>
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
                <tr class="hover:bg-indigo-50/30 transition-colors">
                    <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3 px-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ match($log->log_type) {
                                'reserve'          => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'actual_deduction'  => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
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
                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Tidak ada log anggaran yang sesuai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const labels       = @json($labels);
const dataRealisasi = @json($dataRealisasi);

new Chart(document.getElementById('realisasiChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Realisasi',
            data: dataRealisasi,
            backgroundColor: 'rgba(99,102,241,0.75)',
            borderColor: 'rgba(99,102,241,1)',
            borderWidth: 0,
            borderRadius: 6,
            borderSkipped: false,
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
