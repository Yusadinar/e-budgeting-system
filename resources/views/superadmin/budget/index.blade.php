@extends('layouts.superadmin')
@section('title', 'Budget Overview')
@section('page-title', 'Budget Overview')
@section('page-subtitle', 'Monitoring anggaran semua departemen')

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pagu {{ $year }}</span>
        <p class="text-2xl font-bold text-slate-900 mt-2">{{ \App\Helpers\FormatHelper::rupiah($totalPlan) }}</p>
        <p class="text-xs text-slate-400 mt-1">Seluruh departemen</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-1">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Terpakai</span>
        <p class="text-2xl font-bold text-violet-600 mt-2">{{ \App\Helpers\FormatHelper::rupiah($totalUsed) }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $totalPlan > 0 ? round(($totalUsed / $totalPlan) * 100, 1) : 0 }}% dari total pagu</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card animate-page-delay-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Reserved</span>
        <p class="text-2xl font-bold text-amber-600 mt-2">{{ \App\Helpers\FormatHelper::rupiah($totalReserved) }}</p>
        <p class="text-xs text-slate-400 mt-1">Sedang di-hold untuk pengajuan</p>
    </div>
</div>

{{-- Budget Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-3">
    <div class="p-5 border-b border-slate-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Anggaran per Departemen</h3>
                <p class="text-xs text-slate-400 mt-0.5">Tahun Fiskal {{ $year }}</p>
            </div>
            <div>
                <a href="{{ route('superadmin.budget.export-master', ['year' => $year]) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Master Data
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto pb-2">
        <table class="w-full text-sm whitespace-nowrap min-w-[900px]">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Departemen</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pagu</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Terpakai</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Reserved</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider" style="min-width:140px">Utilisasi</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($departments as $dept)
                <tr class="hover:bg-violet-50/30 transition-colors">
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $dept['name'] }}</td>
                    <td class="py-3 px-4 font-mono text-xs text-slate-400">{{ $dept['budget_code'] }}</td>
                    <td class="py-3 px-4 text-right text-slate-700">{{ \App\Helpers\FormatHelper::rupiah($dept['total_plan']) }}</td>
                    <td class="py-3 px-4 text-right text-violet-600 font-medium">{{ \App\Helpers\FormatHelper::rupiah($dept['total_used']) }}</td>
                    <td class="py-3 px-4 text-right text-amber-600">{{ \App\Helpers\FormatHelper::rupiah($dept['total_reserved']) }}</td>
                    <td class="py-3 px-4 text-right {{ $dept['remaining'] < 0 ? 'text-rose-600' : 'text-emerald-600' }} font-medium">
                        {{ \App\Helpers\FormatHelper::rupiah($dept['remaining']) }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden flex">
                                <div class="h-full bg-rose-500 transition-all duration-700" style="width: {{ $dept['total_plan'] > 0 ? round($dept['total_used'] / $dept['total_plan'] * 100, 1) : 0 }}%"></div>
                                <div class="h-full bg-amber-400 transition-all duration-700" style="width: {{ $dept['total_plan'] > 0 ? round($dept['total_reserved'] / $dept['total_plan'] * 100, 1) : 0 }}%"></div>
                                <div class="h-full bg-emerald-400 transition-all duration-700" style="width: {{ $dept['total_plan'] > 0 ? max(0, 100 - round($dept['total_used'] / $dept['total_plan'] * 100, 1) - round($dept['total_reserved'] / $dept['total_plan'] * 100, 1)) : 0 }}%"></div>
                            </div>
                            <span class="text-xs font-semibold w-10 text-right
                                {{ $dept['utilization'] >= 90 ? 'text-rose-600' : ($dept['utilization'] >= 80 ? 'text-amber-600' : 'text-violet-600') }}">
                                {{ $dept['utilization'] }}%
                            </span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('superadmin.budget.show', $dept['id']) }}"
                           class="inline-flex items-center gap-1 text-xs text-violet-500 hover:text-violet-700 font-medium transition-colors">
                            Lihat
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
