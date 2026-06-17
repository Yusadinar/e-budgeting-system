@extends('layouts.app')
@section('title', 'Menu Pengajuan')
@section('page-title', 'Menu Pengajuan')
@section('page-subtitle', 'Alur: PPBJ → PH → IA (bertahap)')

@section('content')

{{-- Stage Indicator --}}
<div class="mb-6 animate-page">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-50"></div>
        <p class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 relative z-10">Alur Proses Pengajuan Anggaran</p>
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 relative z-10">
            <!-- Step 1: PPBJ -->
            <div class="flex items-center gap-4 w-full sm:w-1/3 p-4 rounded-xl bg-indigo-50/50 border border-indigo-100">
                <div class="w-12 h-12 shrink-0 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-indigo-900">1. PPBJ</h3>
                    <p class="text-[11px] text-indigo-600/80 leading-snug mt-0.5">Permohonan Pengadaan Barang/Jasa</p>
                </div>
            </div>

            <!-- Arrow -->
            <div class="hidden sm:flex flex-col items-center justify-center text-slate-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </div>

            <!-- Step 2: PH -->
            <div class="flex items-center gap-4 w-full sm:w-1/3 p-4 rounded-xl bg-sky-50/50 border border-sky-100">
                <div class="w-12 h-12 shrink-0 rounded-full bg-sky-500 text-white flex items-center justify-center shadow-lg shadow-sky-500/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-sky-900">2. PH</h3>
                    <p class="text-[11px] text-sky-600/80 leading-snug mt-0.5">Pemilihan Vendor & Proposal Harga</p>
                </div>
            </div>

            <!-- Arrow -->
            <div class="hidden sm:flex flex-col items-center justify-center text-slate-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </div>

            <!-- Step 3: IA -->
            <div class="flex items-center gap-4 w-full sm:w-1/3 p-4 rounded-xl bg-emerald-50/50 border border-emerald-100">
                <div class="w-12 h-12 shrink-0 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-900">3. IA</h3>
                    <p class="text-[11px] text-emerald-600/80 leading-snug mt-0.5">Kesepakatan & Internal Agreement</p>
                </div>
            </div>
        </div>
        
        <div class="mt-5 text-xs font-medium text-slate-500 flex items-center gap-2 border-t border-slate-100 pt-3 relative z-10">
            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Setiap dokumen wajib berstatus <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">Approved</span> penuh sebelum dapat membuat dokumen pada tahap berikutnya.</span>
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 animate-page">
    <p class="text-sm font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 inline-flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Total: <span class="text-slate-700 font-bold">{{ $pengajuan->total() }}</span> dokumen
    </p>
    <a href="{{ route('pengajuan.create-ppbj') }}"
       class="group relative inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition-all shadow-[0_4px_12px_rgba(79,70,229,0.3)] hover:shadow-[0_6px_16px_rgba(79,70,229,0.4)] hover:-translate-y-0.5 overflow-hidden w-full sm:w-auto">
        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
        <svg class="w-5 h-5 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        <span class="relative z-10">Buat PPBJ Baru</span>
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
                        <div class="flex flex-col items-start gap-1">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-slate-100 text-slate-600'   => $item->status === 'Draft',
                                'bg-amber-50 text-amber-600'    => $item->status === 'In_Review',
                                'bg-emerald-50 text-emerald-600'=> $item->status === 'Approved',
                                'bg-rose-50 text-rose-600'      => $item->status === 'Rejected',
                            ])>{{ str_replace('_', ' ', $item->status) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $item->created_at->format('d M Y') }}</span>
                            <a href="{{ route('pengajuan.show-ppbj', $item->id) }}" target="_blank" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors mt-0.5">Preview Detail</a>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @if($ph)
                        <div class="flex flex-col items-start gap-1">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-amber-50 text-amber-600'    => $ph->status === 'In_Review',
                                'bg-emerald-50 text-emerald-600'=> $ph->status === 'Approved',
                                'bg-rose-50 text-rose-600'      => $ph->status === 'Rejected',
                            ])>{{ str_replace('_', ' ', $ph->status) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ph->created_at->format('d M Y') }}</span>
                            <a href="{{ route('pengajuan.print-ph', $ph->id) }}" target="_blank" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors mt-0.5">Preview Detail</a>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">{{ $item->isApproved() ? '⏳ Belum dibuat' : '🔒 Locked' }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($ia)
                        <div class="flex flex-col items-start gap-1">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-amber-50 text-amber-600'    => $ia->status_ia === 'In_Review',
                                'bg-emerald-50 text-emerald-600'=> $ia->status_ia === 'Approved',
                                'bg-rose-50 text-rose-600'      => $ia->status_ia === 'Rejected',
                            ])>{{ str_replace('_', ' ', $ia->status_ia) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ia->created_at->format('d M Y') }}</span>
                            <a href="{{ route('pengajuan.print-ia', $ia->id) }}" target="_blank" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors mt-0.5">Preview Detail</a>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">{{ ($ph && $ph->isApproved()) ? '⏳ Belum dibuat' : '🔒 Locked' }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(in_array($item->status, ['Draft', 'Rejected']) || ($item->status === 'In_Review' && (int) $item->approval_step <= 1))
                            <form method="POST" action="{{ route('pengajuan.destroy-ppbj', $item->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus pengajuan {{ $item->ppbj_number }}? Data akan dihapus permanen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-500 hover:text-rose-700 transition-colors">Batalkan</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <p class="text-base font-bold text-slate-700">Belum ada pengajuan</p>
                        <p class="text-sm text-slate-500 mt-1 mb-5">Mulai dengan membuat permohonan pengadaan pertama Anda.</p>
                        <a href="{{ route('pengajuan.create-ppbj') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold transition-colors text-sm border border-indigo-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Buat PPBJ Baru
                        </a>
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
    <div class="sm:hidden space-y-4">
        @forelse($pengajuan as $item)
        @php $ph = $item->latestProposalHarga; $ia = $ph?->internalAgreement; @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-start justify-between gap-3">
                <div>
                    <p class="font-bold text-slate-800 text-sm leading-snug">{{ $item->subject ?? 'PPBJ' }}</p>
                    <p class="font-mono text-[11px] text-slate-500 mt-1">{{ $item->ppbj_number }}</p>
                </div>
                @if(in_array($item->status, ['Draft', 'Rejected']) || ($item->status === 'In_Review' && (int) $item->approval_step <= 1))
                <form method="POST" action="{{ route('pengajuan.destroy-ppbj', $item->id) }}"
                      onsubmit="return confirm('Yakin ingin menghapus pengajuan {{ $item->ppbj_number }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-slate-400 hover:text-rose-500 p-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>

            <!-- Documents Tracker -->
            <div class="p-4 space-y-4">
                <!-- PPBJ Row -->
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">1</span>
                        <span class="font-bold text-slate-700">PPBJ</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span @class([
                            'px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider',
                            'bg-amber-100 text-amber-700'    => $item->status === 'In_Review',
                            'bg-emerald-100 text-emerald-700'=> $item->status === 'Approved',
                            'bg-rose-100 text-rose-700'      => $item->status === 'Rejected',
                            'bg-slate-100 text-slate-600'    => !in_array($item->status, ['In_Review', 'Approved', 'Rejected'])
                        ])>{{ str_replace('_',' ',$item->status) }}</span>
                        <a href="{{ route('pengajuan.show-ppbj', $item->id) }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Detail</a>
                    </div>
                </div>

                <!-- PH Row -->
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full {{ $ph ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-bold text-[10px]">2</span>
                        <span class="font-bold {{ $ph ? 'text-slate-700' : 'text-slate-400' }}">PH</span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($ph)
                            <span @class([
                                'px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider',
                                'bg-amber-100 text-amber-700'    => $ph->status === 'In_Review',
                                'bg-emerald-100 text-emerald-700'=> $ph->status === 'Approved',
                                'bg-rose-100 text-rose-700'      => $ph->status === 'Rejected',
                                'bg-slate-100 text-slate-600'    => !in_array($ph->status, ['In_Review', 'Approved', 'Rejected'])
                            ])>{{ str_replace('_',' ',$ph->status) }}</span>
                            <a href="{{ route('pengajuan.print-ph', $ph->id) }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Detail</a>
                        @else
                            @if($item->isApproved())
                                <a href="{{ route('pengajuan.create-ph', $item->id) }}" class="text-amber-700 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-200">Buat PH →</a>
                            @else
                                <span class="text-slate-300 text-[10px] italic">Menunggu PPBJ</span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- IA Row -->
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full {{ $ia ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-bold text-[10px]">3</span>
                        <span class="font-bold {{ $ia ? 'text-slate-700' : 'text-slate-400' }}">IA</span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($ia)
                            <span @class([
                                'px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider',
                                'bg-amber-100 text-amber-700'    => $ia->status_ia === 'In_Review',
                                'bg-emerald-100 text-emerald-700'=> $ia->status_ia === 'Approved',
                                'bg-rose-100 text-rose-700'      => $ia->status_ia === 'Rejected',
                                'bg-slate-100 text-slate-600'    => !in_array($ia->status_ia, ['In_Review', 'Approved', 'Rejected'])
                            ])>{{ str_replace('_',' ',$ia->status_ia) }}</span>
                            <a href="{{ route('pengajuan.print-ia', $ia->id) }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Detail</a>
                        @else
                            @if($ph && $ph->isApproved())
                                <a href="{{ route('pengajuan.create-ia', $ph->id) }}" class="text-amber-700 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-200">Buat IA →</a>
                            @else
                                <span class="text-slate-300 text-[10px] italic">Menunggu PH</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 text-right">
                <span class="text-[10px] font-medium text-slate-400">Dibuat: {{ $item->created_at->format('d M Y') }}</span>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center text-slate-400 shadow-sm">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="text-base font-bold text-slate-700">Belum ada pengajuan</p>
            <p class="text-sm text-slate-500 mt-1 mb-5">Mulai dengan membuat permohonan pengadaan pertama Anda.</p>
            <a href="{{ route('pengajuan.create-ppbj') }}"
               class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold transition-colors text-sm border border-indigo-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Buat PPBJ Baru
            </a>
        </div>
        @endforelse
    </div>
</div>

@endsection