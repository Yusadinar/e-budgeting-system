@extends('layouts.app')
@section('title', 'Riwayat Pemakaian Anggaran')
@section('page-title', 'Riwayat Pemakaian Anggaran')
@section('page-subtitle', 'Detail pergerakan pagu anggaran departemen Anda')

@section('content')

<div class="mb-6 animate-page flex items-center justify-between">
    <div>
        <h2 class="text-xl font-display text-slate-900">Histori Transaksi</h2>
        <p class="text-sm text-slate-500 mt-0.5">Mendetailkan seluruh dokumen yang menahan atau merealisasikan anggaran.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
        &larr; Kembali ke Dashboard
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm animate-page-delay-1">
    @if(isset($logs) && $logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <th class="p-4 font-semibold">Tanggal & Waktu</th>
                        <th class="p-4 font-semibold">Departemen</th>
                        <th class="p-4 font-semibold">Tipe Transaksi</th>
                        <th class="p-4 font-semibold">No. Referensi</th>
                        <th class="p-4 font-semibold">Keterangan</th>
                        <th class="p-4 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @foreach($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-slate-600 whitespace-nowrap">
                                <div class="font-medium text-slate-700">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="p-4 text-slate-600 whitespace-nowrap">
                                {{ $log->department->dept_name ?? '-' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($log->log_type === 'reserve')
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Hold / Proses
                                    </span>
                                @elseif($log->log_type === 'actual_deduction')
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Terealisasi
                                    </span>
                                @elseif($log->log_type === 'increase')
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Penambahan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200/50">
                                        {{ $log->log_type }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 font-medium text-slate-700 whitespace-nowrap">
                                {{ $log->reference_no }}
                            </td>
                            <td class="p-4 text-slate-600 min-w-[300px]">
                                {{ $log->description }}
                            </td>
                            <td class="p-4 text-right font-semibold whitespace-nowrap 
                                {{ $log->log_type === 'increase' ? 'text-emerald-600' : (in_array($log->log_type, ['reserve', 'actual_deduction']) ? 'text-rose-600' : 'text-slate-800') }}">
                                {{ $log->log_type === 'increase' ? '+' : (in_array($log->log_type, ['reserve', 'actual_deduction']) ? '-' : '') }}Rp {{ number_format($log->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 border border-slate-100 mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-800 mb-1">Belum Ada Riwayat</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto">Departemen ini belum melakukan transaksi pemakaian atau penahanan anggaran sama sekali.</p>
        </div>
    @endif
</div>

@endsection
