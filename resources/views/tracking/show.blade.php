@extends('layouts.app')
@section('title', 'Detail Pengajuan')
@section('page-title', 'Detail Pengajuan')
@section('page-subtitle', $ppbj->ppbj_number)

@section('content')
@php
    $ph = $ppbj->latestProposalHarga;
    $ia = $ph?->internalAgreement;
@endphp
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Back Button --}}
    <div class="mb-2 animate-page">
        <a href="{{ route('tracking.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors bg-white/50 px-3 py-1.5 rounded-lg border border-slate-200/50 hover:bg-white hover:shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Banner Pembeda User --}}
    @if($ppbj->user_id === Auth::id())
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3 flex items-center gap-3 animate-page mb-4">
        <div class="bg-indigo-100 p-1.5 rounded-full text-indigo-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-indigo-800">Ini adalah dokumen pengajuan Anda.</p>
    </div>
    @else
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center gap-3 animate-page mb-4">
        <div class="bg-slate-200 p-1.5 rounded-full text-slate-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <p class="text-sm text-slate-700">Dokumen ini diajukan oleh <strong class="font-bold text-slate-900">{{ $ppbj->user->name }}</strong>.</p>
    </div>
    @endif

    {{-- Header Card --}}
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-5 animate-page">
        <div class="flex justify-between items-start gap-3 sm:gap-4">
            <div class="flex-1 min-w-0">
                <h2 class="text-base font-semibold text-slate-800 break-words">{{ $ph->subject ?? 'Pengajuan PPBJ' }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="font-mono text-xs text-slate-500">{{ $ppbj->ppbj_number }}</span>
                    <span class="text-slate-300">·</span>
                    <span class="text-xs text-slate-500">{{ $ppbj->jenis_label }}</span>
                </div>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row items-end sm:items-start gap-3 sm:gap-5 shrink-0">
                <div class="flex flex-col gap-1 items-end">
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $ppbj->status === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ppbj->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ppbj->status === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ppbj->status === 'Rejected',
                    ])>PPBJ: {{ str_replace('_', ' ', $ppbj->status) }}</span>

                    @if($ph)
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $ph->status === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ph->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ph->status === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ph->status === 'Rejected',
                    ])>PH: {{ str_replace('_', ' ', $ph->status) }}</span>
                    @endif

                    @if($ia)
                    <span @class([
                        'shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                        'bg-slate-100 text-slate-600'   => $ia->status_ia === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ia->status_ia === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ia->status_ia === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ia->status_ia === 'Rejected',
                    ])>IA: {{ str_replace('_', ' ', $ia->status_ia) }}</span>
                    @endif
                </div>

                {{-- QR Code untuk Print/Tracking HP --}}
                <div class="shrink-0 border border-slate-200 p-1 rounded-lg bg-white shadow-sm flex flex-col items-center" title="Scan QR Code ini untuk melihat progress approval dari HP">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('tracking.show', $ppbj->id)) }}" alt="QR Code" class="w-14 h-14 sm:w-16 sm:h-16 object-cover" />
                    <p class="text-[8px] text-slate-500 font-mono mt-1 text-center leading-tight tracking-tighter">SCAN FOR<br>TRACKING</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-5 pt-5 border-t border-slate-100">
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Pengaju</p>
                <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ppbj->user->name }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Departemen</p>
                <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ppbj->user->department?->dept_name ?? '—' }}</p>
            </div>
            @if($ph)
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Nominal PH</p>
                <p class="text-sm font-semibold text-indigo-600 mt-0.5">Rp {{ number_format($ph->nominal_request, 0, ',', '.') }}</p>
            </div>
            @endif
            @if($ia)
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">No. IA</p>
                <p class="text-sm font-mono text-slate-700 mt-0.5">{{ $ia->ia_number }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Nominal Final</p>
                <p class="text-sm font-semibold text-emerald-600 mt-0.5">Rp {{ number_format($ia->final_nominal, 0, ',', '.') }}</p>
            </div>
            @if($ia->sap_doc_no)
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">No. SAP</p>
                <p class="text-sm font-mono text-slate-700 mt-0.5">{{ $ia->sap_doc_no }}</p>
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- Timeline Stepper --}}
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-5 animate-page-delay-1">
        <h3 class="text-sm font-semibold text-slate-800 mb-5">Timeline Approval</h3>
        <div class="relative">
            {{-- Vertical line --}}
            <div class="absolute left-3.5 top-3 bottom-3 w-px bg-slate-100"></div>

            <div class="space-y-5">
                @foreach($timeline as $step)
                <div class="relative flex items-start gap-4 pl-10">
                    {{-- Icon --}}
                    <div @class([
                        'absolute left-0 w-7 h-7 rounded-full flex items-center justify-center shrink-0 ring-4 ring-white',
                        'bg-emerald-500'   => $step['status'] === 'done',
                        'bg-amber-400'     => $step['status'] === 'active',
                        'bg-rose-500'      => $step['status'] === 'rejected',
                        'bg-slate-200'     => $step['status'] === 'pending',
                    ])>
                        @if($step['status'] === 'done')
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        @elseif($step['status'] === 'active')
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        @elseif($step['status'] === 'rejected')
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        @else
                        <div class="w-2 h-2 bg-slate-400 rounded-full"></div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pb-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <p @class([
                                'text-sm font-medium',
                                'text-slate-800'   => in_array($step['status'], ['done', 'active']),
                                'text-rose-600'    => $step['status'] === 'rejected',
                                'text-slate-400'   => $step['status'] === 'pending',
                            ])>{{ $step['label'] }}</p>
                            @if($step['date'])
                            <span class="text-[10px] text-slate-400">{{ $step['date'] }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $step['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Approval Actions PPBJ --}}
    @if(Auth::user()->canApprove() && $ppbj->status === 'In_Review')
    @php
        $canActPpbj = match((int) $ppbj->approval_step) {
            1 => Auth::user()->isKaDept(),
            2 => Auth::user()->isKaDiv(),
            3 => Auth::user()->isAccounting(),
            default => false
        };
    @endphp
    @if($canActPpbj)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: PPBJ</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen ini menunggu persetujuan Anda.</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ppbj.approve', $ppbj->id) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui PPBJ</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ppbj').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak PPBJ</button>
        </div>
    </div>
    
    <div id="modal-reject-ppbj" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl">
            <h3 class="text-base font-semibold text-slate-800 mb-1">Alasan Penolakan PPBJ</h3>
            <form method="POST" action="{{ route('approval.ppbj.reject', $ppbj->id) }}">
                @csrf
                <textarea name="reject_reason" required rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:border-rose-400 outline-none mt-2" placeholder="Alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-reject-ppbj').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-200">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

    {{-- Approval Actions PH --}}
    @if($ph && Auth::user()->canApprove() && $ph->status === 'In_Review')
    @php
        $canActPh = match((int) $ph->approval_step) {
            1 => Auth::user()->isKaDept(),
            2 => Auth::user()->isKaDiv(),
            3 => Auth::user()->isAccounting(),
            default => false
        };
    @endphp
    @if($canActPh)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: Proposal Harga</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen PH menunggu persetujuan Anda.</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ph.approve', $ph->id) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui PH</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ph').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak PH</button>
        </div>
    </div>
    
    <div id="modal-reject-ph" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl">
            <h3 class="text-base font-semibold text-slate-800 mb-1">Alasan Penolakan PH</h3>
            <form method="POST" action="{{ route('approval.ph.reject', $ph->id) }}">
                @csrf
                <textarea name="reject_reason" required rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm mt-2" placeholder="Alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-reject-ph').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-200">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

    {{-- Approval Actions IA --}}
    @if($ia && Auth::user()->canApprove() && $ia->status_ia === 'In_Review')
    @php
        $canActIa = match((int) $ia->approval_step) {
            1 => Auth::user()->isKaDept(),
            2 => Auth::user()->isKaDiv(),
            3 => Auth::user()->isKaDeptAcc(),
            4 => Auth::user()->isKaDivAcc(),
            5 => Auth::user()->isFinDir(),
            6 => Auth::user()->isManDir(),
            7 => Auth::user()->isPresDir(),
            default => false
        };
    @endphp
    @if($canActIa)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: Internal Agreement</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen IA menunggu persetujuan Anda.</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ia.approve', $ia->id) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui IA</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ia').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak IA</button>
        </div>
    </div>
    
    <div id="modal-reject-ia" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl">
            <h3 class="text-base font-semibold text-slate-800 mb-1">Alasan Penolakan IA</h3>
            <form method="POST" action="{{ route('approval.ia.reject', $ia->id) }}">
                @csrf
                <textarea name="reject_reason" required rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm mt-2" placeholder="Alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-reject-ia').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-200">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

</div>
@endsection