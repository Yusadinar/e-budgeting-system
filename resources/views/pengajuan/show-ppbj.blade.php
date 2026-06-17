@extends('layouts.app')
@section('title', 'Detail PPBJ — ' . $ppbj->ppbj_number)
@section('page-title', 'Detail PPBJ')
@section('page-subtitle', $ppbj->ppbj_number)
@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 5mm;
    }
    body { 
        background:#fff!important; 
        zoom: 85%; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact; 
    }
    #sidebar, #sidebar-overlay, header, footer, .no-print { display:none!important; }
    #main-content { margin:0!important; }
    main { padding:0!important; }
    .paper-wrapper { overflow: visible!important; }
    .paper { box-shadow:none!important; border:none!important; padding: 0!important; margin: 0!important; width: 100%!important; box-sizing: border-box!important; min-height: auto!important; display: block!important; }
    .paper::before { display: none; }
}
.paper-wrapper {
    overflow-x: auto;
    padding-bottom: 24px;
}
.paper {
    background:#fff; width:210mm; min-height:297mm; margin:0 auto;
    border:1px solid #cbd5e1; border-radius:4px;
    box-shadow:0 4px 24px rgba(0,0,0,.08);
    padding:24px 32px; font-size:11px; color:#000; line-height:1.4;
    position:relative; display:flex; flex-direction:column;
}
.paper::before { content:''; position:absolute; top:0; left:0; right:0; height:6px; background:#000; border-radius:4px 4px 0 0; }
.doc-title { text-align:center; font-size:16px; font-weight:700; color:#000; text-decoration:underline; text-underline-offset:4px; margin-bottom:4px; text-transform:uppercase; }
.doc-sub { text-align:center; font-size:11px; color:#000; margin-bottom:16px; font-weight:bold; }
.doc-number { text-align:center; font-size:13px; font-weight:bold; color:#000; margin-bottom:20px; }
.info-grid { display:grid; grid-template-columns:1fr 1fr; border-top:1px solid #000; border-left:1px solid #000; }
.info-cell { padding:6px 10px; border-bottom:1px solid #000; border-right:1px solid #000; }
.info-cell.full { grid-column:1/-1; }
.info-label { font-size:10px; font-weight:bold; color:#000; margin-bottom:2px; text-transform:uppercase; }
.info-value { font-size:11px; color:#000; }
.section-head { background:#f3f4f6; font-size:11px; font-weight:bold; color:#000; padding:6px 10px; text-transform:uppercase; border-bottom:1px solid #000; border-right:1px solid #000; grid-column:1/-1; }
.sign-table { width:100%; border-collapse:collapse; margin-top:10px; border:1px solid #000; }
.sign-table td,.sign-table th { border:1px solid #000; padding:4px 6px; font-size:10px; text-align:center; color:#000; }
.sign-table th { background:#f3f4f6; font-weight:bold; }
.sign-box { height:70px; }
.align-table { width: 100%; border-collapse: collapse; border: none; margin: 0; }
.align-table td { padding: 1px 0; border: none; vertical-align: top; font-size: 11px; color: #000; text-align: left; }
.align-table td.lbl { width: 65px; color: #000; font-weight:bold; }
.align-table td.cln { width: 10px; text-align: center; color: #000; font-weight:bold; }
/* @media(max-width:768px) { .paper { padding:16px; width:100%; } .info-grid { grid-template-columns:1fr; } } */
</style>
@endpush

@section('content')
<div class="max-w-[210mm] mx-auto w-full mb-4 relative flex items-center justify-between no-print animate-page">
    <button type="button" onclick="if(window.history.length <= 1) { window.close(); } else { history.back(); }" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors bg-white/50 px-3 py-1.5 rounded-lg border border-slate-200/50 hover:bg-white hover:shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline">Kembali</span>
        <span class="sm:hidden">Back</span>
    </button>
    
    <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 hidden sm:flex">
        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold tracking-widest uppercase shadow-sm border border-black/5" style="{{ $ppbj->status === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($ppbj->status === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $ppbj->status) }}
        </span>
    </div>

    <div class="flex items-center gap-3">
        <span class="sm:hidden inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm" style="{{ $ppbj->status === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($ppbj->status === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $ppbj->status) }}
        </span>
        <button onclick="window.print()" class="justify-center inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span class="hidden sm:inline">Cetak Dokumen</span>
            <span class="sm:hidden">Cetak</span>
        </button>
    </div>
</div>

<div class="sm:hidden text-center text-[11px] text-slate-500 mb-2 no-print animate-page-delay-1 flex items-center justify-center gap-1.5 bg-indigo-50 py-1.5 rounded-lg max-w-[210mm] mx-auto">
    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
    <span>Geser area dokumen untuk melihat seluruh isi form</span>
</div>

<div class="paper-wrapper">
    <div class="paper animate-page" style="font-family: Arial, Helvetica, sans-serif;">
        <div class="p-4 flex-1 flex flex-col">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <img src="{{ asset('images/ippi-logo.png') }}" alt="Logo" style="height:40px;">
                <div style="text-align:right;font-size:10px;color:#000;font-weight:bold;display:flex;flex-direction:column;align-items:flex-end;">
                    PT INTI PANTJA PRESS INDUSTRI
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('tracking.show', $ppbj->id)) }}" alt="QR Code" style="width:60px;height:60px;margin-top:6px;" />
                </div>
            </div>
            <div class="doc-title">Permohonan Pengadaan Barang / Jasa (PPBJ)</div>
            <div class="doc-sub">No. PPBJ: {{ $ppbj->ppbj_number }}</div>

            <div class="info-grid mb-2">
                <div class="section-head">A. Data Umum</div>
                <div class="info-cell"><div class="info-label">Department/Section</div><div class="info-value">{{ $ppbj->department_section }}</div></div>
                <div class="info-cell"><div class="info-label">IA No.</div><div class="info-value">&nbsp;</div></div>
                <div class="info-cell"><div class="info-label">Subject</div><div class="info-value">{{ $ppbj->subject }}</div></div>
                <div class="info-cell"><div class="info-label">IO/FR No.</div><div class="info-value">&nbsp;</div></div>
                <div class="info-cell"><div class="info-label">Nama Barang/Jasa</div><div class="info-value">{{ $ppbj->nama_barang_jasa }}</div></div>
                <div class="info-cell"><div class="info-label">Qty & UoM</div><div class="info-value">{{ $ppbj->qty }} {{ $ppbj->uom }}</div></div>
                <div class="info-cell"><div class="info-label">Spesifikasi</div><div class="info-value">{{ $ppbj->spesifikasi }}</div></div>
                <div class="info-cell"><div class="info-label">Pernah Order</div><div class="info-value">{{ ucfirst($ppbj->pernah_order) }}{{ $ppbj->pernah_order === 'sudah' ? ' — ' . $ppbj->pernah_order_bulan : '' }}</div></div>

                <div class="section-head">B. Background 5W+1H & Risk</div>
                <div class="info-cell">
                    <div class="info-label">Background / Problem</div>
                    <table class="align-table">
                    @foreach(['what'=>'What','why'=>'Why','when'=>'When','where'=>'Where','who'=>'Who','how'=>'How'] as $k => $l)
                        <tr>
                            <td class="lbl">{{ strtolower(chr(ord('a') + $loop->index)) }}. {{ $l }}</td>
                            <td class="cln">:</td>
                            <td style="padding-bottom:6px;">{{ $ppbj->{'bg_'.$k} }}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
                <div class="info-cell"><div class="info-label">Risk Analysis</div><div class="info-value" style="white-space:pre-wrap;">{{ $ppbj->risk_analysis }}</div></div>

                <div class="section-head">C. Condition & Spec</div>
                <div class="info-cell" style="grid-row:span 2;">
                    <div class="info-label">Condition Photo</div>
                    @if($ppbj->condition_photo)<img src="{{ asset('storage/'.$ppbj->condition_photo) }}" style="max-height:140px;border-radius:4px;">@else <span class="info-value">—</span> @endif
                </div>
                <div class="info-cell">
                    <div class="info-label">Detail Specification</div>
                    <table class="align-table">
                        <tr><td class="lbl">Brand</td><td class="cln">:</td><td>{{ $ppbj->spec_brand }}</td></tr>
                        <tr><td class="lbl">Maker</td><td class="cln">:</td><td>{{ $ppbj->spec_maker }}</td></tr>
                        <tr><td class="lbl">Negara</td><td class="cln">:</td><td>{{ $ppbj->spec_negara_asal }}</td></tr>
                        <tr><td class="lbl">Lain</td><td class="cln">:</td><td>{{ $ppbj->spec_lain_lain ?: '—' }}</td></tr>
                    </table>
                </div>
                <div class="info-cell" style="grid-row:span 2;">
                    <div class="info-label">Urgency</div>
                    <table class="align-table">
                        <tr><td class="lbl">Level</td><td class="cln">:</td><td>{{ ucfirst($ppbj->urgency_level) }}</td></tr>
                        <tr><td class="lbl">Line stop</td><td class="cln">:</td><td>{{ $ppbj->potensi_line_stop ?: '—' }}</td></tr>
                        @if($ppbj->urgency_options)
                        <tr><td class="lbl">Options</td><td class="cln">:</td><td>{{ implode(', ', array_map(fn($o) => str_replace('_',' ',$o), $ppbj->urgency_options)) }}</td></tr>
                        @endif
                    </table>
                </div>
                <div class="info-cell" style="display:flex; flex-direction:column; justify-content:center;">
                    <div class="info-label">Budget/Estimasi</div>
                    <table class="align-table">
                        <tr><td class="lbl">Tipe</td><td class="cln">:</td><td>{{ strtoupper($ppbj->budget_type) }}</td></tr>
                        <tr><td class="lbl">Range</td><td class="cln">:</td><td>{{ App\Models\Ppbj::budgetAmountRanges()[$ppbj->budget_amount_range] ?? $ppbj->budget_amount_range }}</td></tr>
                    </table>
                </div>

                <div class="section-head">D. Layout Area</div>
                <div class="info-cell">
                    <div class="info-label">Layout Photo</div>
                    @if($ppbj->layout_photo)<img src="{{ asset('storage/'.$ppbj->layout_photo) }}" style="max-height:140px;border-radius:4px;">@else <span class="info-value">—</span> @endif
                </div>
                <div class="info-cell">
                    <div class="info-label">Lokasi Penggunaan</div>
                    <table class="align-table">
                        <tr><td class="lbl">Pressline</td><td class="cln">:</td><td>{{ $ppbj->lokasi_pressline ?: '—' }}</td></tr>
                        <tr><td class="lbl">Sub-assy</td><td class="cln">:</td><td>{{ $ppbj->lokasi_sub_assy ?: '—' }}</td></tr>
                        <tr><td class="lbl">Metal Finish</td><td class="cln">:</td><td>{{ $ppbj->lokasi_metal_finish ?: '—' }}</td></tr>
                        <tr><td class="lbl">Lain</td><td class="cln">:</td><td>{{ $ppbj->lokasi_lain_lain ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            @php
                $approvalsByStep = $ppbj->approvals->keyBy('step');
                
                $columns = [
                    5 => 'President Director',
                    4 => 'Finance Director',
                    3 => 'Ka. Div Finance',
                    2 => 'Ka. Sie Purc.',
                    1 => 'Ka. Dept Terkait',
                ];
            @endphp

            <div class="mt-auto">
                <table class="sign-table">
                    <thead><tr>
                        <th colspan="3">DISETUJUI</th>
                        <th colspan="2">DIPERIKSA</th>
                        <th>DIBUAT</th>
                    </tr></thead>
                    <tbody>
                        {{-- Row 1: Jabatan --}}
                        <tr>
                            @foreach($columns as $step => $label)
                            <td style="font-size:10px;font-weight:bold;">{{ strtoupper($label) }}</td>
                            @endforeach
                            <td style="font-size:10px;font-weight:bold;">{{ strtoupper($ppbj->user->role_label) }}</td>
                        </tr>
                        {{-- Row 2: Tanda tangan --}}
                        <tr>
                            @php
                                $rangePPBJ = $ppbj->budget_amount_range;
                                $maxStepPPBJ = 3; // Default sampai Ka. Div Finance (<= 50jt)
                                if ($rangePPBJ === '50m_to_100m') {
                                    $maxStepPPBJ = 4; // Sampai Finance Director
                                } elseif (in_array($rangePPBJ, ['100m_to_500m', '500m_to_1b', 'over_1b'])) {
                                    $maxStepPPBJ = 5; // Sampai President Director
                                }
                            @endphp
                            @foreach($columns as $step => $label)
                            <td class="sign-box" style="vertical-align:middle;text-align:center;overflow:hidden; position:relative;">
                                @if($step > $maxStepPPBJ)
                                    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0.2; pointer-events: none;">
                                        <svg style="width: 100%; height: 100%; color: black;" viewBox="0 0 100 100" preserveAspectRatio="none">
                                            <line x1="0" y1="0" x2="100" y2="100" stroke="currentColor" stroke-width="1.5" />
                                            <line x1="100" y1="0" x2="0" y2="100" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                @elseif($approval = $approvalsByStep->get($step))
                                    @if($approval->action === 'approved' && $approval->signature_data)
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                            <img src="{{ $approval->signature_data }}" alt="TTD" style="height:35px;transform:scale(1.8);transform-origin:center; mix-blend-mode: multiply; filter: grayscale(100%);">
                                        </div>
                                    @elseif($approval->action === 'rejected')
                                        <span style="font-size:10px;font-weight:bold;">✕ DITOLAK</span>
                                    @endif
                                @elseif((int) $ppbj->approval_step === $step && $ppbj->status === 'In_Review')
                                    <span style="font-size:9px;">Menunggu</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="sign-box" style="vertical-align:middle;text-align:center;overflow:hidden;">
                                @if($approvalCreator = $approvalsByStep->get(0))
                                    @if($approvalCreator->signature_data)
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                            <img src="{{ $approvalCreator->signature_data }}" alt="TTD" style="height:35px;transform:scale(1.8);transform-origin:center; mix-blend-mode: multiply; filter: grayscale(100%);">
                                        </div>
                                    @else
                                        <span style="font-size:9px;font-weight:bold;">✓ Diajukan</span>
                                    @endif
                                @else
                                    <span style="font-size:9px;font-weight:bold;">✓ Diajukan</span>
                                @endif
                            </td>
                        </tr>
                        {{-- Row 3: Nama --}}
                        <tr>
                            @foreach($columns as $step => $label)
                            <td style="font-size:10px;font-weight:bold;">
                                @if($approval = $approvalsByStep->get($step))
                                    {{ strtoupper($approval->user->name) }}
                                @elseif($step === 2 && !isset($approvalsByStep[2]))
                                    BRAMANSYAH B.I.
                                @else
                                    —
                                @endif
                            </td>
                            @endforeach
                            <td style="font-size:10px;font-weight:bold;">
                                {{ strtoupper($ppbj->user->name) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:6px;font-size:9px;font-weight:bold;">
                    *** Keterangan: ≤ 50jt: Kadiv | 50jt-100jt: s/d Direktur | > 100jt: s/d Presdir
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
