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
                    @php
                        $rawItems = [];
                        if ($ph && is_array($ph->items_data)) {
                            $rawItems = $ph->items_data;
                        } elseif ($ph && is_string($ph->items_data)) {
                            $rawItems = json_decode($ph->items_data, true) ?? [];
                        }

                        $itemsList = isset($rawItems['items']) ? $rawItems['items'] : $rawItems;
                        if (!empty($itemsList) && !isset($itemsList[0])) {
                            $itemsList = [$itemsList];
                        }
                    @endphp

                    @if(count($itemsList) > 0)
                        @foreach($itemsList as $item)
                            <span class="text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded font-medium whitespace-nowrap">
                                {{ $item['description'] ?? '-' }} ({{ $item['qty'] ?? 0 }} {{ strtoupper($item['uom'] ?? '') }})
                            </span>
                        @endforeach
                    @else
                        <span class="text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded font-medium">{{ $ppbj->nama_barang_jasa ?? '—' }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row items-end sm:items-start gap-3 sm:gap-5 shrink-0">
                <div class="flex flex-col gap-1.5 items-stretch">
                    <span @class([
                        'flex justify-center items-center gap-2 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider',
                        'bg-slate-100 text-slate-600'   => $ppbj->status === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ppbj->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ppbj->status === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ppbj->status === 'Rejected',
                    ])>
                        <span>PPBJ:</span>
                        <span>{{ str_replace('_', ' ', $ppbj->status) }}</span>
                    </span>

                    @if($ph)
                    <span @class([
                        'flex justify-center items-center gap-2 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider',
                        'bg-slate-100 text-slate-600'   => $ph->status === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ph->status === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ph->status === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ph->status === 'Rejected',
                    ])>
                        <span>PH:</span>
                        <span>{{ str_replace('_', ' ', $ph->status) }}</span>
                    </span>
                    @endif

                    @if($ia)
                    <span @class([
                        'flex justify-center items-center gap-2 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider',
                        'bg-slate-100 text-slate-600'   => $ia->status_ia === 'Draft',
                        'bg-amber-50 text-amber-600 ring-1 ring-amber-200'    => $ia->status_ia === 'In_Review',
                        'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200'=> $ia->status_ia === 'Approved',
                        'bg-rose-50 text-rose-600 ring-1 ring-rose-200'      => $ia->status_ia === 'Rejected',
                    ])>
                        <span>IA:</span>
                        <span>{{ str_replace('_', ' ', $ia->status_ia) }}</span>
                    </span>
                    @endif
                </div>

                {{-- QR Code untuk Print/Tracking HP --}}
                <div class="shrink-0 border border-slate-200 p-1 rounded-lg bg-white shadow-sm flex flex-col items-center" title="Scan QR Code ini untuk melihat progress approval dari HP">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('tracking.show', $ppbj->id)) }}" alt="QR Code" class="w-14 h-14 sm:w-16 sm:h-16 object-cover" />
                    <p class="text-[8px] text-slate-500 font-mono mt-1 text-center leading-tight tracking-tighter">SCAN FOR<br>TRACKING</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-5 pt-5 border-t border-slate-100">
            {{-- Kolom 1: Informasi Pengaju --}}
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Nama Pengaju</p>
                    <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $ppbj->user->name }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Departemen</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ppbj->user->department?->dept_name ?? '—' }}</p>
                </div>
                @if($ph && $ph->cost_center)
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Cost Center</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ph->cost_center }}</p>
                </div>
                @endif
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Tanggal Dibuat</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ppbj->created_at?->format('d M Y, H:i') ?? '—' }}</p>
                </div>
            </div>

            {{-- Kolom 2: Informasi Keuangan --}}
            <div class="space-y-4">
                {{-- PH Block --}}
                <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-50">
                    <p class="text-[10px] text-indigo-400 uppercase tracking-wide font-medium">Nominal Request (PH)</p>
                    @if($ph)
                        <p class="text-sm font-bold text-indigo-700 mt-0.5">Rp {{ number_format($ph->nominal_request, 0, ',', '.') }}</p>
                        <p class="text-[10px] font-medium text-indigo-600 mt-1 truncate" title="{{ $ph->selected_vendor_name ?? 'Vendor Penawaran' }}">
                            Vendor: {{ $ph->selected_vendor_name ?? '—' }}
                        </p>
                    @else
                        <p class="text-sm font-bold text-slate-400 mt-0.5">—</p>
                    @endif
                </div>

                {{-- IA Block --}}
                <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-50">
                    <p class="text-[10px] text-emerald-500 uppercase tracking-wide font-medium">Nominal Final (IA)</p>
                    @if($ia)
                        <p class="text-sm font-bold text-emerald-700 mt-0.5">Rp {{ number_format($ia->final_nominal, 0, ',', '.') }}</p>
                        @if($ph && $ph->nominal_request > 0 && $ph->nominal_request > $ia->final_nominal)
                            @php
                                $saving = $ph->nominal_request - $ia->final_nominal;
                                $percentage = ($saving / $ph->nominal_request) * 100;
                            @endphp
                            <p class="text-[10px] font-medium text-emerald-600 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Hemat Rp {{ number_format($saving, 0, ',', '.') }} ({{ number_format($percentage, 1, ',', '.') }}%)
                            </p>
                        @elseif($ph && $ph->nominal_request > 0 && $ph->nominal_request < $ia->final_nominal)
                            @php
                                $over = $ia->final_nominal - $ph->nominal_request;
                            @endphp
                            <p class="text-[10px] font-medium text-rose-600 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                Naik Rp {{ number_format($over, 0, ',', '.') }}
                            </p>
                        @endif
                    @else
                        <p class="text-sm font-bold text-slate-400 mt-0.5">—</p>
                    @endif
                </div>
            </div>

            {{-- Kolom 3: Referensi Nomor (Di Kanan) --}}
            <div class="space-y-4 p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">No. PPBJ</p>
                    <p class="text-xs font-mono font-medium text-slate-700 mt-0.5">{{ $ppbj->ppbj_number }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">No. Proposal Harga</p>
                    <p class="text-xs font-mono font-medium {{ $ph ? 'text-slate-700' : 'text-slate-400' }} mt-0.5">{{ $ph->ph_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">No. Internal Agreement</p>
                    <p class="text-xs font-mono font-medium {{ $ia ? 'text-slate-700' : 'text-slate-400' }} mt-0.5">{{ $ia->ia_number ?? '—' }}</p>
                </div>
                <div class="pt-2 border-t border-slate-200">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">Dokumen SAP</p>
                    <p class="text-xs font-mono font-bold {{ $ia && $ia->sap_doc_no ? 'text-slate-800' : 'text-slate-400' }} mt-0.5">{{ $ia->sap_doc_no ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Dokumen Preview (Printable) --}}
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-5 animate-page-delay-1">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Preview Dokumen Lengkap</h3>
            <span class="text-[10px] text-slate-400">Klik untuk melihat detail fisik (A4)</span>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pengajuan.show-ppbj', $ppbj->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 hover:text-indigo-800 rounded-xl hover:bg-indigo-100 transition-colors text-sm font-medium border border-indigo-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview PPBJ
            </a>
            
            @if($ph)
            <a href="{{ route('pengajuan.print-ph', $ph->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 hover:text-indigo-800 rounded-xl hover:bg-indigo-100 transition-colors text-sm font-medium border border-indigo-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview PH
            </a>
            @elseif($ppbj->isApproved() && (Auth::user()->username === 'bramansyah.badar' || (Auth::user()->isKaSie() && Auth::user()->section === 'Purchasing & Import')))
            <a href="{{ route('pengajuan.create-ph', $ppbj->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 hover:text-amber-800 rounded-xl hover:bg-amber-100 transition-colors text-sm font-bold border border-amber-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Buat Proposal Harga
            </a>
            @endif

            @if($ia)
            <a href="{{ route('pengajuan.print-ia', $ia->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 hover:text-indigo-800 rounded-xl hover:bg-indigo-100 transition-colors text-sm font-medium border border-indigo-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview IA
            </a>
            @elseif($ph && $ph->isApproved() && (Auth::user()->username === 'susan.anggraeni' || Auth::user()->section === 'Budget & Sistem Informasi'))
            <a href="{{ route('pengajuan.create-ia', $ph->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 hover:text-amber-800 rounded-xl hover:bg-amber-100 transition-colors text-sm font-bold border border-amber-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Buat Internal Agreement
            </a>
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
                @if(isset($step['type']) && $step['type'] === 'divider')
                <div class="relative flex items-center gap-4 pl-10 py-1">
                    <div class="absolute left-0 w-7 h-7 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0 ring-4 ring-white z-10">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                    </div>
                    <div class="flex-1 relative flex items-center">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-dashed border-slate-200"></div>
                        </div>
                        <span class="relative bg-white pr-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $step['label'] }}</span>
                    </div>
                </div>
                @else
                <div class="relative flex items-start gap-4 pl-10">
                    {{-- Icon --}}
                    <div @class([
                        'absolute left-0 w-7 h-7 rounded-full flex items-center justify-center shrink-0 ring-4 ring-white z-10',
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
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Approval Actions PPBJ --}}
    @if(Auth::user()->canApprove() && $ppbj->status === 'In_Review')
    @php
        $isFinAcc = str_contains(strtolower(Auth::user()->department?->dept_name ?? ''), 'finance accounting');
        $isKaSiePurchasing = Auth::user()->isKaSie() && Auth::user()->section === 'Purchasing & Import';
        $canActPpbj = match((int) $ppbj->approval_step) {
            1 => Auth::user()->isKaDept() && $ppbj->user->dept_id === Auth::user()->dept_id,
            2 => $isKaSiePurchasing,
            3 => Auth::user()->isKaDiv() && $isFinAcc,
            4 => Auth::user()->isFinDir(),
            5 => Auth::user()->isPresDir(),
            default => false
        };
    @endphp
    @if($canActPpbj)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: PPBJ</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen ini menunggu persetujuan Anda. Silakan bubuhkan tanda tangan digital terlebih dahulu.</p>

        {{-- Signature Pad --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-600 mb-2">Tanda Tangan Digital <span class="text-rose-500">*</span></label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-1 bg-slate-50 relative" style="touch-action:none;">
                <canvas id="signaturePad" width="460" height="160" class="w-full rounded-lg bg-white cursor-crosshair" style="max-width:100%;height:160px;"></canvas>
                <button type="button" onclick="clearSignature()" class="absolute top-2 right-2 text-[10px] px-2 py-1 rounded bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors">Hapus</button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Gambar tanda tangan Anda menggunakan mouse atau jari (touchscreen).</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ppbj.approve', $ppbj->id) }}" class="flex-1" id="formApprovePpbj">
                @csrf
                <input type="hidden" name="signature_data" id="signatureInput">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui PPBJ</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ppbj').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak PPBJ</button>
        </div>
    </div>

    {{-- Signature Pad Script --}}
    <script>
    (function() {
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let lastX = 0, lastY = 0;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            if (e.touches && e.touches.length > 0) {
                return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
            }
            return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
        }

        function startDraw(e) { 
            e.preventDefault();
            drawing = true; 
            const pos = getPos(e); 
            lastX = pos.x; lastY = pos.y; 
        }
        function draw(e) { 
            if (!drawing) return; 
            e.preventDefault();
            const pos = getPos(e);
            ctx.beginPath(); 
            ctx.moveTo(lastX, lastY); 
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#1e293b'; 
            ctx.lineWidth = 2.5; 
            ctx.lineCap = 'round'; 
            ctx.lineJoin = 'round';
            ctx.stroke();
            lastX = pos.x; lastY = pos.y;
        }
        function stopDraw() { drawing = false; }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        window.clearSignature = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        };

        function isCanvasBlank() {
            const blankCanvas = document.createElement('canvas');
            blankCanvas.width = canvas.width;
            blankCanvas.height = canvas.height;
            return canvas.toDataURL() === blankCanvas.toDataURL();
        }

        document.getElementById('formApprovePpbj').addEventListener('submit', function(e) {
            if (isCanvasBlank()) {
                e.preventDefault();
                alert('Harap bubuhkan tanda tangan digital terlebih dahulu.');
                return false;
            }
            document.getElementById('signatureInput').value = canvas.toDataURL('image/png');
        });
    })();
    </script>
    
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
            1 => Auth::user()->username === 'bramansyah.badar',
            2 => Auth::user()->username === 'fauzan.nurdinsyah',
            3 => Auth::user()->username === 'fadillah.ahmad',
            4 => Auth::user()->username === 'budiwijayanti.riana',
            5 => Auth::user()->username === 'riana.budiwijayanti',
            6 => Auth::user()->username === 'yoga.dina',
            default => false
        };
    @endphp
    @if($canActPh)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: Proposal Harga</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen PH menunggu persetujuan Anda. Silakan bubuhkan tanda tangan digital terlebih dahulu.</p>

        {{-- Signature Pad PH --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-600 mb-2">Tanda Tangan Digital <span class="text-rose-500">*</span></label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-1 bg-slate-50 relative" style="touch-action:none;">
                <canvas id="signaturePadPh" width="460" height="160" class="w-full rounded-lg bg-white cursor-crosshair" style="max-width:100%;height:160px;"></canvas>
                <button type="button" onclick="clearSignaturePh()" class="absolute top-2 right-2 text-[10px] px-2 py-1 rounded bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors">Hapus</button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Gambar tanda tangan Anda menggunakan mouse atau jari (touchscreen).</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ph.approve', $ph->id) }}" class="flex-1" id="formApprovePh">
                @csrf
                <input type="hidden" name="signature_data" id="signatureInputPh">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui PH</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ph').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak PH</button>
        </div>
    </div>

    {{-- Signature Pad Script PH --}}
    <script>
    (function() {
        const canvas = document.getElementById('signaturePadPh');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let lastX = 0, lastY = 0;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            if (e.touches && e.touches.length > 0) {
                return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
            }
            return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
        }

        function startDraw(e) { 
            e.preventDefault();
            drawing = true; 
            const pos = getPos(e); 
            lastX = pos.x; lastY = pos.y; 
        }
        function draw(e) { 
            if (!drawing) return; 
            e.preventDefault();
            const pos = getPos(e);
            ctx.beginPath(); 
            ctx.moveTo(lastX, lastY); 
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#1e293b'; 
            ctx.lineWidth = 2.5; 
            ctx.lineCap = 'round'; 
            ctx.lineJoin = 'round';
            ctx.stroke();
            lastX = pos.x; lastY = pos.y;
        }
        function stopDraw() { drawing = false; }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        window.clearSignaturePh = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        };

        function isCanvasBlank() {
            const blankCanvas = document.createElement('canvas');
            blankCanvas.width = canvas.width;
            blankCanvas.height = canvas.height;
            return canvas.toDataURL() === blankCanvas.toDataURL();
        }

        document.getElementById('formApprovePh').addEventListener('submit', function(e) {
            if (isCanvasBlank()) {
                e.preventDefault();
                alert('Harap bubuhkan tanda tangan digital terlebih dahulu.');
                return false;
            }
            document.getElementById('signatureInputPh').value = canvas.toDataURL('image/png');
        });
    })();
    </script>
    
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
        $submitter = $ia->proposalHarga->ppbj->user;
        $isFinAcc = str_contains(strtolower(Auth::user()->department?->dept_name ?? ''), 'finance accounting');
        $canActIa = match((int) $ia->approval_step) {
            1 => Auth::user()->isKaSie() && Auth::user()->dept_id === $submitter->dept_id && Auth::user()->section === $submitter->section,
            2 => Auth::user()->isKaDept() && $submitter->dept_id === Auth::user()->dept_id,
            3 => Auth::user()->isKaDiv() && $submitter->dept_id === Auth::user()->dept_id,
            4 => Auth::user()->isKaDept() && $isFinAcc,
            5 => Auth::user()->isProdDir() || Auth::user()->isManDir() || Auth::user()->isPresDir(),
            6 => Auth::user()->isFinDir(),
            default => false
        };

        if ((int)$ia->approval_step === 1 && $submitter->id === Auth::id() && Auth::user()->isKaSie()) {
            $canActIa = true;
        }
    @endphp
    @if($canActIa)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-card p-5 animate-page-delay-1">
        <p class="text-sm font-semibold text-slate-800 mb-1">Tindakan Approval: Internal Agreement</p>
        <p class="text-xs text-slate-400 mb-4">Dokumen IA menunggu persetujuan Anda. Silakan bubuhkan tanda tangan digital terlebih dahulu.</p>

        {{-- Signature Pad IA --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-600 mb-2">Tanda Tangan Digital <span class="text-rose-500">*</span></label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-1 bg-slate-50 relative" style="touch-action:none;">
                <canvas id="signaturePadIa" width="460" height="160" class="w-full rounded-lg bg-white cursor-crosshair" style="max-width:100%;height:160px;"></canvas>
                <button type="button" onclick="clearSignatureIa()" class="absolute top-2 right-2 text-[10px] px-2 py-1 rounded bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors">Hapus</button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Gambar tanda tangan Anda menggunakan mouse atau jari (touchscreen).</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('approval.ia.approve', $ia->id) }}" class="flex-1" id="formApproveIa">
                @csrf
                <input type="hidden" name="signature_data" id="signatureInputIa">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">✓ Setujui IA</button>
            </form>
            <button onclick="document.getElementById('modal-reject-ia').classList.remove('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border-2 border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium transition-all">✕ Tolak IA</button>
        </div>
    </div>
    
    {{-- Signature Pad Script IA --}}
    <script>
    (function() {
        const canvas = document.getElementById('signaturePadIa');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let lastX = 0, lastY = 0;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            if (e.touches && e.touches.length > 0) {
                return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
            }
            return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
        }

        function startDraw(e) { 
            e.preventDefault();
            drawing = true; 
            const pos = getPos(e); 
            lastX = pos.x; lastY = pos.y; 
        }
        function draw(e) { 
            if (!drawing) return; 
            e.preventDefault();
            const pos = getPos(e);
            ctx.beginPath(); 
            ctx.moveTo(lastX, lastY); 
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#1e293b'; 
            ctx.lineWidth = 2.5; 
            ctx.lineCap = 'round'; 
            ctx.lineJoin = 'round';
            ctx.stroke();
            lastX = pos.x; lastY = pos.y;
        }
        function stopDraw() { drawing = false; }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        window.clearSignatureIa = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        };

        function isCanvasBlank() {
            const blankCanvas = document.createElement('canvas');
            blankCanvas.width = canvas.width;
            blankCanvas.height = canvas.height;
            return canvas.toDataURL() === blankCanvas.toDataURL();
        }

        document.getElementById('formApproveIa').addEventListener('submit', function(e) {
            if (isCanvasBlank()) {
                e.preventDefault();
                alert('Harap bubuhkan tanda tangan digital terlebih dahulu.');
                return false;
            }
            document.getElementById('signatureInputIa').value = canvas.toDataURL('image/png');
        });
    })();
    </script>
    
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