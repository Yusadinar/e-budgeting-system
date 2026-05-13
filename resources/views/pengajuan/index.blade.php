@extends('layouts.app')
@section('title', 'Menu Pengajuan')
@section('page-title', 'Menu Pengajuan')
@section('page-subtitle', 'Alur: PPBJ → PH → IA (bertahap)')

@section('content')

{{-- Stage Indicator --}}
<div class="mb-5 animate-page">
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Alur Pengajuan</p>
        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                <span class="text-xs font-semibold text-indigo-600">PPBJ</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-1 max-w-[40px]"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-xs font-bold">2</div>
                <span class="text-xs font-medium text-slate-400">PH</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-1 max-w-[40px]"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-xs font-bold">3</div>
                <span class="text-xs font-medium text-slate-400">IA</span>
            </div>
            <div class="ml-auto text-[10px] text-slate-400 hidden sm:block">* Setiap tahap harus approved sebelum lanjut</div>
        </div>
    </div>
</div>

<div class="flex items-center justify-between mb-5 animate-page">
    <p class="text-sm text-slate-500">{{ $pengajuan->total() }} total pengajuan</p>
    <a href="{{ route('pengajuan.create-ppbj') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700
              text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Buat PPBJ Baru
    </a>
</div>

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
                        <span class="text-slate-700 font-medium block mt-1">{{ $item->subject ?? 'PPBJ' }}</span>
                        <span class="block text-xs text-slate-400 mt-0.5">{{ $item->nama_barang_jasa ?? '' }}</span>
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
                            'bg-amber-50 text-amber-600'    => $ph->status === 'In_Review',
                            'bg-emerald-50 text-emerald-600'=> $ph->status === 'Approved',
                            'bg-rose-50 text-rose-600'      => $ph->status === 'Rejected',
                        ])>{{ str_replace('_', ' ', $ph->status) }}</span>
                        @else
                        <span class="text-xs text-slate-400">{{ $item->isApproved() ? '⏳ Belum dibuat' : '🔒 Locked' }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($ia)
                        <span @class([
                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                            'bg-amber-50 text-amber-600'    => $ia->status_ia === 'In_Review',
                            'bg-emerald-50 text-emerald-600'=> $ia->status_ia === 'Approved',
                            'bg-rose-50 text-rose-600'      => $ia->status_ia === 'Rejected',
                        ])>{{ str_replace('_', ' ', $ia->status_ia) }}</span>
                        @else
                        <span class="text-xs text-slate-400">{{ ($ph && $ph->isApproved()) ? '⏳ Belum dibuat' : '🔒 Locked' }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-400">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('pengajuan.show-ppbj', $item->id) }}"
                               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                Detail
                            </a>
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
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <p class="text-sm font-medium">Belum ada pengajuan</p>
                        <p class="text-xs mt-1">Buat PPBJ pertama Anda</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($pengajuan->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $pengajuan->links() }}</div>
        @endif
    </div>

    {{-- CARD LIST (mobile) --}}
    <div class="sm:hidden space-y-3">
        @forelse($pengajuan as $item)
        @php $ph = $item->latestProposalHarga; $ia = $ph?->internalAgreement; @endphp
        <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-4">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-medium text-slate-800 text-sm">{{ $item->subject ?? 'PPBJ' }}</p>
                    <p class="font-mono text-xs text-slate-400 mt-0.5">{{ $item->ppbj_number }}</p>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-amber-50 text-amber-600'    => $item->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600'=> $item->status === 'Approved',
                        'bg-rose-50 text-rose-600'      => $item->status === 'Rejected',
                    ])>PPBJ: {{ str_replace('_',' ',$item->status) }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ $item->created_at->format('d M Y') }}</span>
                <div class="flex gap-2">
                    <a href="{{ route('pengajuan.show-ppbj', $item->id) }}" class="text-xs font-medium text-indigo-600">Detail</a>
                    @if(!$ph && $item->isApproved())
                    <a href="{{ route('pengajuan.create-ph', $item->id) }}" class="text-xs font-medium text-amber-600">Buat PH →</a>
                    @elseif($ph && !$ia && $ph->isApproved())
                    <a href="{{ route('pengajuan.create-ia', $ph->id) }}" class="text-xs font-medium text-amber-600">Buat IA →</a>
                    @endif
                </div>
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