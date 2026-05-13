@extends('layouts.app')
@section('title', 'Monitoring Departemen')
@section('page-title', 'Monitoring Departemen')
@section('page-subtitle')
    Ringkasan anggaran & pengajuan seluruh departemen — FY{{ $year }}
@endsection

@section('content')

{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 animate-page">
    <div>
        <h2 class="text-xl font-display text-slate-900">Semua Departemen</h2>
        <p class="text-sm text-slate-500 mt-0.5">{{ $departments->count() }} departemen terdaftar · FY{{ $year }}</p>
    </div>
    <form method="GET" action="{{ route('director.departments.index') }}" class="flex items-center gap-2">
        <label class="text-xs text-slate-500 font-medium">Tahun:</label>
        <select name="year" onchange="this.form.submit()"
                class="text-sm rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

{{-- Summary KPI Strip --}}
<div class="grid grid-cols-3 gap-4 mb-6 animate-page">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Pagu</span>
        <p class="text-lg font-bold text-slate-900 mt-1.5">Rp {{ number_format($totalPlan / 1_000_000_000, 2, ',', '.') }} M</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Terpakai</span>
        <p class="text-lg font-bold text-indigo-600 mt-1.5">Rp {{ number_format($totalUsed / 1_000_000_000, 2, ',', '.') }} M</p>
        @if($totalPlan > 0)
        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
            @php $pctGlobal = round(($totalUsed/$totalPlan)*100, 1); @endphp
            <div class="h-full rounded-full {{ $pctGlobal >= 85 ? 'bg-rose-500' : ($pctGlobal >= 65 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                 style="width: {{ min($pctGlobal, 100) }}%"></div>
        </div>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $pctGlobal }}% terpakai</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Reserved</span>
        <p class="text-lg font-bold text-amber-600 mt-1.5">Rp {{ number_format($totalReserved / 1_000_000_000, 2, ',', '.') }} M</p>
    </div>
</div>

{{-- Department Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($departments as $i => $dept)
    @php
        $pct = $dept['utilization'];
        $barColor = $pct >= 90 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : ($pct >= 50 ? 'bg-indigo-500' : 'bg-emerald-500'));
        $badgeColor = $pct >= 90 ? 'bg-rose-100 text-rose-700' : ($pct >= 75 ? 'bg-amber-100 text-amber-700' : ($pct >= 50 ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700'));
        $delay = ['', 'animate-page-delay-1', 'animate-page-delay-2', 'animate-page-delay-3'][$i % 4];
    @endphp
    <a href="{{ route('director.departments.show', $dept['id']) }}?year={{ $year }}"
       class="group bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover hover:border-indigo-200 transition-all duration-200 {{ $delay }} flex flex-col gap-4">

        {{-- Dept Header --}}
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-slate-50 flex items-center justify-center shrink-0 group-hover:from-indigo-100 transition-colors">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors leading-tight">{{ $dept['name'] }}</p>
                    <p class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $dept['budget_code'] }}</p>
                </div>
            </div>
            <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-full {{ $badgeColor }}">
                {{ $pct }}%
            </span>
        </div>

        {{-- Budget Progress --}}
        <div>
            <div class="flex justify-between text-[11px] text-slate-500 mb-1.5">
                <span>Pagu: <span class="font-semibold text-slate-700">Rp {{ number_format($dept['plan'] / 1_000_000, 0, ',', '.') }} Jt</span></span>
                <span>Terpakai: <span class="font-semibold text-slate-700">Rp {{ number_format($dept['used'] / 1_000_000, 0, ',', '.') }} Jt</span></span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ min($pct, 100) }}%"></div>
            </div>
            <div class="flex justify-between text-[11px] mt-1.5">
                <span class="text-slate-400">Reserved: Rp {{ number_format($dept['reserved'] / 1_000_000, 0, ',', '.') }} Jt</span>
                <span class="{{ $dept['sisa'] < 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600' }}">
                    Sisa: Rp {{ number_format($dept['sisa'] / 1_000_000, 0, ',', '.') }} Jt
                </span>
            </div>
        </div>

        {{-- Footer Stats --}}
        <div class="flex items-center justify-between pt-3 border-t border-slate-50 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                {{ $dept['users_count'] }} anggota
            </span>
            @if($dept['pengajuan_aktif'] > 0)
            <span class="flex items-center gap-1.5 text-amber-600">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $dept['pengajuan_aktif'] }} pengajuan aktif
            </span>
            @else
            <span class="text-emerald-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tidak ada antrian
            </span>
            @endif
            <span class="flex items-center gap-1 text-indigo-500 font-medium group-hover:gap-2 transition-all">
                Detail
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </span>
        </div>
    </a>
    @empty
    <div class="col-span-3 bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-card">
        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/>
            </svg>
        </div>
        <p class="text-slate-500 text-sm">Belum ada departemen yang terdaftar</p>
    </div>
    @endforelse
</div>

@endsection
