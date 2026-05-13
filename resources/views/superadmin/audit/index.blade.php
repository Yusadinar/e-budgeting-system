@extends('layouts.superadmin')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'Riwayat perubahan anggaran seluruh departemen')

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card mb-5 animate-page">
    <form method="GET" action="{{ route('superadmin.audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-3">
        <select name="dept_id"
                class="w-full lg:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
            <option value="">Semua Departemen</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('dept_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->dept_name }}
                </option>
            @endforeach
        </select>

        <select name="log_type"
                class="w-full lg:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
            <option value="">Semua Tipe</option>
            @foreach($logTypes as $type)
                <option value="{{ $type }}" {{ request('log_type') === $type ? 'selected' : '' }}>
                    {{ match($type) {
                        'reserve'          => 'Hold / Reserve',
                        'actual_deduction' => 'Realisasi Terpakai',
                        'increase'         => 'Penambahan Pagu',
                        'reclass'          => 'Reklasifikasi',
                        default            => $type
                    } }}
                </option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="w-full lg:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">

        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="w-full lg:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">

        <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
            <button type="submit"
                    class="flex-1 lg:flex-none px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['dept_id', 'log_type', 'date_from', 'date_to']))
                <a href="{{ route('superadmin.audit.index') }}"
                   class="flex-1 lg:flex-none px-4 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-sm font-medium hover:bg-slate-50 transition-colors text-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Audit Log Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-1">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Departemen</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tipe</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Referensi</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nominal</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                <tr class="hover:bg-violet-50/30 transition-colors">
                    <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3 px-4 text-sm text-slate-700">{{ $log->department?->dept_name ?? '—' }}</td>
                    <td class="py-3 px-4">
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
                    <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $log->reference_no }}</td>
                    <td class="py-3 px-4 text-right font-medium text-slate-700 whitespace-nowrap">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-xs text-slate-500 max-w-[300px] truncate">{{ $log->description ?: '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                            <p>Tidak ada log ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
