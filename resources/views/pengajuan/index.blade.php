@extends('layouts.app')
@section('title', 'Menu Pengajuan')
@section('page-title', 'Menu Pengajuan')
@section('page-subtitle', 'Daftar semua pengajuan anggaran Anda')

@section('content')

<div class="flex items-center justify-between mb-5 animate-page">
    <div>
        <p class="text-sm text-slate-500">{{ $pengajuan->total() }} total pengajuan ditemukan</p>
    </div>
    <a href="{{ route('pengajuan.create-ppbj') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700
              text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Pengajuan Baru
    </a>
</div>

{{-- Mobile: Card list | Desktop: Table --}}
<div class="animate-page-delay-1">

    {{-- TABLE (desktop) --}}
    <div class="hidden sm:block bg-white rounded-1xl border border-slate-100 shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengajuan</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">PPBJ</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">PH</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">IA</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($pengajuan as $item)
                @php 
                    $ph = $item->latestProposalHarga; 
                    $ia = $ph?->internalAgreement;
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <span class="font-mono text-xs text-slate-600 block">{{ $item->ppbj_number }}</span>
                        <span class="text-slate-700 font-medium block mt-1">{{ $ph?->subject ?? 'Belum ada PH' }}</span>
                        <span class="block text-xs text-slate-400 mt-0.5">{{ $item->jenis_label }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span @class([
                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                            'bg-slate-100 text-slate-600'   => $item->status === 'Draft',
                            'bg-amber-50 text-amber-600'    => $item->status === 'In_Review',
                            'bg-emerald-50 text-emerald-600'=> $item->status === 'Approved',
                            'bg-rose-50 text-rose-600'      => $item->status === 'Rejected',
                        ])>{{ str_replace('_', ' ', $item->status) }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($ph)
                        <span @class([
                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                            'bg-slate-100 text-slate-600'   => $ph->status === 'Draft',
                            'bg-amber-50 text-amber-600'    => $ph->status === 'In_Review',
                            'bg-emerald-50 text-emerald-600'=> $ph->status === 'Approved',
                            'bg-rose-50 text-rose-600'      => $ph->status === 'Rejected',
                        ])>{{ str_replace('_', ' ', $ph->status) }}</span>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($ia)
                        <span @class([
                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                            'bg-slate-100 text-slate-600'   => $ia->status_ia === 'Draft',
                            'bg-amber-50 text-amber-600'    => $ia->status_ia === 'In_Review',
                            'bg-emerald-50 text-emerald-600'=> $ia->status_ia === 'Approved',
                            'bg-rose-50 text-rose-600'      => $ia->status_ia === 'Rejected',
                        ])>{{ str_replace('_', ' ', $ia->status_ia) }}</span>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-400">
                        {{ $item->created_at->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if(!$ph && $item->isApproved())
                            <a href="{{ route('pengajuan.create-ph', $item->id) }}"
                               class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                                Buat PH →
                            </a>
                        @elseif($ph && !$ia && $ph->isApproved())
                            <a href="{{ route('pengajuan.create-ia', $ph->id) }}"
                               class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                                Buat IA →
                            </a>
                        @else
                            <a href="{{ route('tracking.show', $item->id) }}"
                               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                Tracking →
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <p class="text-sm font-medium">Belum ada pengajuan</p>
                        <p class="text-xs mt-1">Buat pengajuan pertama Anda</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($pengajuan->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $pengajuan->links() }}
        </div>
        @endif
    </div>

    {{-- CARD LIST (mobile) --}}
    <div class="sm:hidden space-y-3">
        @forelse($pengajuan as $item)
        @php 
            $ph = $item->latestProposalHarga;
            $ia = $ph?->internalAgreement;
        @endphp
        <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-4">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-medium text-slate-800 text-sm">{{ $ph?->subject ?? 'Belum ada PH' }}</p>
                    <p class="font-mono text-xs text-slate-400 mt-0.5">{{ $item->ppbj_number }}</p>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $item->status === 'Draft',
                        'bg-amber-50 text-amber-600'    => $item->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600'=> $item->status === 'Approved',
                        'bg-rose-50 text-rose-600'      => $item->status === 'Rejected',
                    ])>PPBJ: {{ $item->status }}</span>

                    @if($ph)
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $ph->status === 'Draft',
                        'bg-amber-50 text-amber-600'    => $ph->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600'=> $ph->status === 'Approved',
                        'bg-rose-50 text-rose-600'      => $ph->status === 'Rejected',
                    ])>PH: {{ $ph->status }}</span>
                    @endif

                    @if($ia)
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $ia->status_ia === 'Draft',
                        'bg-amber-50 text-amber-600'    => $ia->status_ia === 'In_Review',
                        'bg-emerald-50 text-emerald-600'=> $ia->status_ia === 'Approved',
                        'bg-rose-50 text-rose-600'      => $ia->status_ia === 'Rejected',
                    ])>IA: {{ $ia->status_ia }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">
                    {{ $ph ? 'Rp ' . number_format($ph->nominal_request, 0, ',', '.') : '—' }}
                </p>
                
                @if(!$ph && $item->isApproved())
                    <a href="{{ route('pengajuan.create-ph', $item->id) }}"
                       class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                        Buat PH →
                    </a>
                @elseif($ph && !$ia && $ph->isApproved())
                    <a href="{{ route('pengajuan.create-ia', $ph->id) }}"
                       class="text-xs font-medium text-amber-600 hover:text-amber-800 transition-colors">
                        Buat IA →
                    </a>
                @else
                    <a href="{{ route('tracking.show', $item->id) }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        Tracking →
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-1xl border border-slate-100 p-8 text-center text-slate-400">
            <p class="text-sm font-medium">Belum ada pengajuan</p>
        </div>
        @endforelse
    </div>
</div>

@endsection