@extends('layouts.app')
@section('title', 'Detail PPBJ — ' . $ppbj->ppbj_number)
@section('page-title', 'Detail PPBJ')
@section('page-subtitle', $ppbj->ppbj_number)

@push('styles')
<style>
@media print {
    #sidebar, #sidebar-overlay, header, footer, .no-print { display:none!important; }
    #main-content { margin:0!important; }
    main { padding:0!important; }
    .paper { box-shadow:none!important; border:none!important; }
    body { background:#fff!important; }
}
.paper {
    background:#fff; max-width:900px; margin:0 auto;
    border:1px solid #cbd5e1; border-radius:4px;
    box-shadow:0 4px 24px rgba(0,0,0,.08);
    padding:32px 40px; font-size:13px; color:#1e293b; line-height:1.5;
    position:relative;
}
.paper::before { content:''; position:absolute; top:0; left:0; right:0; height:6px; background:linear-gradient(90deg,#4f46e5,#6366f1,#818cf8); border-radius:4px 4px 0 0; }
.doc-title { text-align:center; font-size:16px; font-weight:700; color:#1e1b4b; margin-bottom:2px; }
.doc-sub { text-align:center; font-size:11px; color:#64748b; margin-bottom:16px; }
.doc-number { text-align:center; font-size:13px; font-weight:600; color:#4f46e5; margin-bottom:20px; padding:6px 0; border:1px dashed #c7d2fe; background:#eef2ff; border-radius:4px; }
.info-grid { display:grid; grid-template-columns:1fr 1fr; border:1px solid #cbd5e1; }
.info-cell { padding:8px 12px; border:1px solid #e2e8f0; }
.info-cell.full { grid-column:1/-1; }
.info-label { font-size:11px; font-weight:600; color:#475569; margin-bottom:2px; }
.info-value { font-size:12px; color:#1e293b; }
.section-head { background:#f1f5f9; font-size:12px; font-weight:700; color:#334155; padding:8px 12px; text-transform:uppercase; letter-spacing:.5px; border:1px solid #e2e8f0; grid-column:1/-1; }
.sign-table { width:100%; border-collapse:collapse; margin-top:16px; }
.sign-table td,.sign-table th { border:1px solid #e2e8f0; padding:6px 10px; font-size:11px; text-align:center; }
.sign-table th { background:#f1f5f9; font-weight:600; }
.sign-box { height:50px; }
.status-badge { display:inline-flex; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; }
.btn-print { display:inline-flex; align-items:center; gap:6px; padding:8px 20px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:500; cursor:pointer; }
.btn-print:hover { background:#e2e8f0; }
@media(max-width:768px) { .paper { padding:16px; } .info-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="no-print" style="text-align:center;margin-bottom:16px;">
    <button onclick="window.print()" class="btn-print">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
        Cetak / Print PDF
    </button>
    <span class="status-badge" style="margin-left:12px;{{ $ppbj->status === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($ppbj->status === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
        {{ str_replace('_', ' ', $ppbj->status) }}
    </span>
</div>

<div class="paper animate-page">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <img src="{{ asset('images/ippi_logo.jpg') }}" alt="Logo" style="height:48px;">
        <div style="text-align:right;font-size:10px;color:#64748b;">PT INTI PANTJA PRESS INDUSTRI</div>
    </div>
    <div class="doc-title">PERMOHONAN PENGADAAN BARANG / JASA (PPBJ)</div>
    <div class="doc-sub">Tanggal: {{ $ppbj->created_at->format('d M Y H:i') }} WIB</div>
    <div class="doc-number">No PPBJ: {{ $ppbj->ppbj_number }}</div>

    <div class="info-grid">
        <div class="section-head">A. Data Umum</div>
        <div class="info-cell"><div class="info-label">Department/Section</div><div class="info-value">{{ $ppbj->department_section }}</div></div>
        <div class="info-cell"><div class="info-label">IA No.</div><div class="info-value">{{ $ppbj->ia_no ?: '—' }}</div></div>
        <div class="info-cell"><div class="info-label">Subject</div><div class="info-value">{{ $ppbj->subject }}</div></div>
        <div class="info-cell"><div class="info-label">IO/FR No.</div><div class="info-value">{{ $ppbj->io_fr_no ?: '—' }}</div></div>
        <div class="info-cell"><div class="info-label">Nama Barang/Jasa</div><div class="info-value">{{ $ppbj->nama_barang_jasa }}</div></div>
        <div class="info-cell"><div class="info-label">Qty & UoM</div><div class="info-value">{{ $ppbj->qty }} {{ $ppbj->uom }}</div></div>
        <div class="info-cell"><div class="info-label">Spesifikasi</div><div class="info-value">{{ $ppbj->spesifikasi }}</div></div>
        <div class="info-cell"><div class="info-label">Pernah Order</div><div class="info-value">{{ ucfirst($ppbj->pernah_order) }}{{ $ppbj->pernah_order === 'sudah' ? ' — ' . $ppbj->pernah_order_bulan : '' }}</div></div>

        <div class="section-head">B. Background 5W+1H & Risk</div>
        <div class="info-cell">
            <div class="info-label">Background / Problem</div>
            @foreach(['what'=>'What','why'=>'Why','when'=>'When','where'=>'Where','who'=>'Who','how'=>'How'] as $k => $l)
            <div style="margin-bottom:4px;"><strong style="color:#6366f1;font-size:11px;">{{ $l }}:</strong> <span class="info-value">{{ $ppbj->{'bg_'.$k} }}</span></div>
            @endforeach
        </div>
        <div class="info-cell"><div class="info-label">Risk Analysis</div><div class="info-value" style="white-space:pre-wrap;">{{ $ppbj->risk_analysis }}</div></div>

        <div class="section-head">C. Condition & Spec</div>
        <div class="info-cell">
            <div class="info-label">Condition Photo</div>
            @if($ppbj->condition_photo)<img src="{{ asset('storage/'.$ppbj->condition_photo) }}" style="max-height:180px;border-radius:4px;">@else <span class="info-value">—</span> @endif
        </div>
        <div class="info-cell">
            <div class="info-label">Detail Specification</div>
            <div class="info-value">Brand: {{ $ppbj->spec_brand }}<br>Maker: {{ $ppbj->spec_maker }}<br>Negara: {{ $ppbj->spec_negara_asal }}<br>Lain: {{ $ppbj->spec_lain_lain ?: '—' }}</div>
            <hr style="margin:8px 0;border-color:#e2e8f0;">
            <div class="info-label">Urgency</div>
            <div class="info-value">Level: {{ ucfirst($ppbj->urgency_level) }}<br>Line stop: {{ $ppbj->potensi_line_stop ?: '—' }}</div>
            @if($ppbj->urgency_options)<div class="info-value" style="margin-top:4px;">Options: {{ implode(', ', array_map(fn($o) => str_replace('_',' ',$o), $ppbj->urgency_options)) }}</div>@endif
            <hr style="margin:8px 0;border-color:#e2e8f0;">
            <div class="info-label">Budget/Estimasi</div>
            <div class="info-value">Tipe: {{ strtoupper($ppbj->budget_type) }}<br>Range: {{ App\Models\Ppbj::budgetAmountRanges()[$ppbj->budget_amount_range] ?? $ppbj->budget_amount_range }}</div>
        </div>

        <div class="section-head">D. Layout Area</div>
        <div class="info-cell">
            <div class="info-label">Layout Photo</div>
            @if($ppbj->layout_photo)<img src="{{ asset('storage/'.$ppbj->layout_photo) }}" style="max-height:180px;border-radius:4px;">@else <span class="info-value">—</span> @endif
        </div>
        <div class="info-cell">
            <div class="info-label">Lokasi Penggunaan</div>
            <div class="info-value">Pressline: {{ $ppbj->lokasi_pressline ?: '—' }}<br>Sub-assy: {{ $ppbj->lokasi_sub_assy ?: '—' }}<br>Metal Finish: {{ $ppbj->lokasi_metal_finish ?: '—' }}<br>Lain: {{ $ppbj->lokasi_lain_lain ?: '—' }}</div>
        </div>
    </div>

    <table class="sign-table">
        <thead><tr>
            <th colspan="3" style="background:#eef2ff;color:#4f46e5;">Disetujui ***</th>
            <th colspan="2" style="background:#f0fdf4;color:#16a34a;">Diperiksa</th>
            <th style="background:#fefce8;color:#ca8a04;">Dibuat</th>
        </tr></thead>
        <tbody>
            <tr>
                <td style="font-size:10px;color:#64748b;">Kadiv Incharge</td>
                <td style="font-size:10px;color:#64748b;">Direktur Incharge</td>
                <td style="font-size:10px;color:#64748b;">Presdir</td>
                <td style="font-size:10px;color:#64748b;">Ka.sie Purch</td>
                <td style="font-size:10px;color:#64748b;">Ka.dept M Rajief</td>
                <td style="font-size:10px;color:#64748b;">Ka Sie: Deddy S</td>
            </tr>
            <tr><td class="sign-box"></td><td class="sign-box"></td><td class="sign-box"></td><td class="sign-box"></td><td class="sign-box"></td><td class="sign-box"></td></tr>
        </tbody>
    </table>
    <div style="margin-top:6px;font-size:10px;color:#94a3b8;">
        <strong>***</strong> ≤ 50jt: Kadiv | 50jt-100jt: s/d Direktur | > 100jt: s/d Presdir
    </div>
</div>
@endsection
